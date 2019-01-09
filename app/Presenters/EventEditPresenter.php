<?php declare(strict_types=1);

namespace App\Presenters;

use App\Forms\FormFactory;
use App\Model\Department;
use App\Model\Employee;
use App\Model\Event;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

final class EventEditPresenter extends Presenter
{
	/**
	 * @var Event
	 */
	private $event;

	/**
	 * @var FormFactory
	 */
	private $formFactory;

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	public function __construct(
		FormFactory $formFactory,
		EntityManagerInterface $entityManager
	) {
		parent::__construct();
		$this->formFactory = $formFactory;
		$this->entityManager = $entityManager;
	}

	public function actionDefault(int $id): void
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('SignIn:');
		} elseif (!$this->event = $this->entityManager->getRepository(Event::class)->find($id)) {
			$this->error();
		} elseif ($this->event->getManager()->getId() !== $this->getUser()->getId()) {
			$this->error();
		}
	}

	protected function createComponentForm(): Form
	{
		$form = $this->formFactory->create();

		$form->addText('name', 'Name')
			->setDefaultValue($this->event->getName())
			->setRequired();
		$form->addDatePicker('date', 'Date')
			->setDefaultValue($this->event->getDate())
			->setRequired();
		$form->addText('price', 'Price $')
			->setType('number')
			->setDefaultValue($this->event->getPrice())
			->setRequired();
		$form->addTextArea('description', 'Description')
			->setDefaultValue($this->event->getDescription());

		/** @var Department[] $departments */
		$departments = $this->entityManager->getRepository(Department::class)->findBy([
			'manager' => $this->getUser()->getId(),
		]);
		$departmentsOptions = [];
		foreach ($departments as $department) {
			$departmentsOptions[$department->getId()] = $department->getCode();
		}
		$form->addMultiSelect('departments', 'Departments', $departmentsOptions)
			->setDefaultValue(array_map(function (Department $department) {
				return $department->getId();
			}, $this->event->getDepartments()));

		$qb = $this->entityManager->createQueryBuilder();
		$qb->select('e.id', 'e.name');
		$qb->from(Employee::class, 'e');
		$qb->join('e.department', 'd');
		$qb->where($qb->expr()->eq('d.manager', ':manager'));
		$qb->setParameter('manager', $this->event->getManager());
		$form->addCheckboxList('employees', 'Employees', array_column($qb->getQuery()->getResult(), 'name', 'id'))
			->setDefaultValue(array_map(function (Employee $employee) {
				return $employee->getId();
			}, $this->event->getEmployees()));

		$form->addSubmit('submit', 'Save');

		$form->onSuccess[] = function (Form $form) {
			$values = $form->getValues();

			$this->event->setName($values->name);
			$this->event->setDate($values->date);
			$this->event->setPrice((float)$values->price);
			$this->event->setDescription($values->description);
			$this->event->setDepartments(...$this->entityManager->getRepository(Department::class)->findBy([
				'id' => $values->departments,
			]));
			$this->event->setEmployees(...$this->entityManager->getRepository(Employee::class)->findBy([
				'id' => $values->employees
			]));

			$this->entityManager->flush();

			$this->flashMessage('Event successfully edited', 'success');
			$this->redirect('EventList:');
		};

		return $form;
	}
}
