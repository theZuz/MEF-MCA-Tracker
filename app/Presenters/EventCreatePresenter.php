<?php declare(strict_types=1);

namespace App\Presenters;

use App\Forms\FormFactory;
use App\Model\Department;
use App\Model\Employee;
use App\Model\Event;
use App\Model\Manager;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Presenter;
use Nette\Forms\Form;

final class EventCreatePresenter extends Presenter
{
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

	public function actionDefault(): void
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('SignIn:');
		}
	}

	protected function createComponentForm(): Form
	{
		$form = $this->formFactory->create();

		$form->addText('name', 'Name')
			->setRequired();
		$form->addDatePicker('date', 'Date')
			->setDefaultValue(new \DateTime)
			->setRequired();
		$form->addText('price', 'Price $')
			->setType('number')
			->setRequired();
		$form->addTextArea('description', 'Description');

		/** @var Department[] $departments */
		$departments = $this->entityManager->getRepository(Department::class)->findBy([
			'manager' => $this->getUser()->getId(),
		]);
		$departmentsOptions = [];
		foreach ($departments as $department) {
			$departmentsOptions[$department->getId()] = $department->getCode();
		}
		$form->addMultiSelect('departments', 'Departments', $departmentsOptions);
		$form->addSubmit('submit', 'Create');

		$form->onSuccess[] = function (Form $form) {
			$values = $form->getValues();

			$event = new Event(
				$this->entityManager->getRepository(Manager::class)->find($this->getUser()->getId()),
				$values->name,
				$values->date,
				(float)$values->price
			);
			$event->setDescription($values->description);
			$event->setDepartments(...$this->entityManager->getRepository(Department::class)->findBy([
				'id' => $values->departments,
			]));
			$event->setEmployees(...$this->entityManager->getRepository(Employee::class)->findBy([
				'department' => $values->departments,
			]));

			$this->entityManager->persist($event);
			$this->entityManager->flush();

			$this->flashMessage('Event successfully created', 'success');
			$this->redirect('EventList:');
		};

		return $form;
	}
}
