<?php declare(strict_types=1);

namespace App\Presenters;

use App\Forms\FormFactory;
use App\Model\Department;
use App\Model\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

final class HomepagePresenter extends Presenter
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

	public function renderDefault(): void
	{
		$qb = $this->entityManager->createQueryBuilder();
		$qb->select('e');
		$qb->from(Employee::class, 'e');
		$qb->join('e.department', 'd');
		$qb->where($qb->expr()->eq('d.manager', ':manager'));
		$qb->setParameter('manager', $this->getUser()->getId());

		$this->template->employees = $qb->getQuery()->getResult();
		$this->template->events = [];
	}

	protected function createComponentFilterForm(): Form
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
		$form->addSelect('departments', 'Department', $departmentsOptions)
			->setPrompt('');

		return $form;
	}
}
