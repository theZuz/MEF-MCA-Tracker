<?php declare(strict_types=1);

namespace App\Presenters;

use App\Forms\FormFactory;
use App\Model\Department;
use App\Model\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Giggsey\Locale\Locale;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

final class EmployeeEditPresenter extends Presenter
{
	/**
	 * @var Employee
	 */
	private $employee;

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
		} elseif (!$this->employee = $this->entityManager->getRepository(Employee::class)->find($id)) {
			$this->error();
		} elseif ($this->employee->getDepartment()->getManager()->getId() !== $this->getUser()->getId()) {
			$this->error();
		}
	}

	protected function createComponentForm(): Form
	{
		$form = $this->formFactory->create();

		/** @var Department[] $departments */
		$departments = $this->entityManager->getRepository(Department::class)->findBy([
			'manager' => $this->getUser()->getId(),
		]);
		$departmentsOptions = [];
		foreach ($departments as $department) {
			$departmentsOptions[$department->getId()] = $department->getCode();
		}
		$form->addSelect('department', 'Department', $departmentsOptions)
			->setDefaultValue($this->employee->getDepartment()->getId())
			->setRequired();
		$form->addText('cnum', 'CNUM')
			->setDefaultValue($this->employee->getCnum())
			->setRequired();
		$form->addText('name', 'Name')
			->setDefaultValue($this->employee->getName())
			->setRequired();
		$countries = Locale::getAllCountriesForLocale('en');
		$form->addSelect('country', 'Country', array_combine(array_keys($countries), array_keys($countries)))
			->setDefaultValue($this->employee->getCountry());
		$form->addDatePicker('startDate', 'Start date')
			->setDefaultValue($this->employee->getStartDate())
			->setNullable();
		$form->addSubmit('submit', 'Edit');

		$form->onSuccess[] = function (Form $form) {
			$values = $form->getValues();

			$this->employee->setDepartment($this->entityManager->getRepository(Department::class)->find($values->department));
			$this->employee->setCnum($values->cnum);
			$this->employee->setName($values->name);
			$this->employee->setCountry($values->country);
			$this->employee->setStartDate($values->startDate);
			$this->entityManager->flush();

			$this->flashMessage('Employee successfully edited', 'success');
			$this->redirect('EmployeeList:', ['grid-filter[department]' => $values->department]);
		};

		return $form;
	}
}
