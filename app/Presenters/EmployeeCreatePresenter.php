<?php declare(strict_types=1);

namespace App\Presenters;

use App\Forms\FormFactory;
use App\Model\Department;
use App\Model\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Giggsey\Locale\Locale;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

final class EmployeeCreatePresenter extends Presenter
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

		/** @var Department[] $departments */
		$departments = $this->entityManager->getRepository(Department::class)->findBy([
			'manager' => $this->getUser()->getId(),
		]);
		$departmentsOptions = [];
		foreach ($departments as $department) {
			$departmentsOptions[$department->getId()] = $department->getCode();
		}
		$form->addSelect('department', 'Department', $departmentsOptions)
			->setRequired();
		$form->addText('cnum', 'CNUM')
			->setRequired();
		$form->addText('name', 'Name')
			->setRequired();
		$countries = Locale::getAllCountriesForLocale('en');
		$form->addSelect('country', 'Country', array_combine(array_keys($countries), array_keys($countries)))
			->setDefaultValue('CZ');
		$form->addDatePicker('startDate', 'Start date')
			->setNullable();
		$form->addSubmit('submit', 'Create');

		$form->onSuccess[] = function (Form $form) {
			$values = $form->getValues();

			$employee = new Employee(
				$this->entityManager->getRepository(Department::class)->find($values->department),
				$values->cnum,
				$values->name,
				$values->country
			);
			$employee->setStartDate($values->startDate);
			$this->entityManager->persist($employee);
			$this->entityManager->flush();

			$this->flashMessage('Employee successfully created', 'success');
			$this->redirect('EmployeeList:');
		};

		return $form;
	}
}
