<?php declare(strict_types=1);

namespace App\Presenters;

use App\Forms\FormFactory;
use App\Model\Department;
use App\Model\Employee;
use App\Model\Event;
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

		if ($department = $this['filterForm']['department']->getValue()) {
			$qb->andWhere($qb->expr()->eq('e.department', ':department'));
			$qb->setParameter('department', $department);
		}

		$this->template->employees = $qb->getQuery()->getResult();

		$qb = $this->entityManager->createQueryBuilder();
		$qb->select('e');
		$qb->from(Event::class, 'e');
		$qb->where($qb->expr()->eq('e.manager', ':manager'));
		$qb->setParameter('manager', $this->getUser()->getId());

		if ($year = $this['filterForm']['year']->getValue()) {
			$qb->andWhere($qb->expr()->between('e.date', ':from', ':to'));
			$qb->setParameter('from', (new \DateTime)->setDate($year, 1, 1));
			$qb->setParameter('to', (new \DateTime)->setDate($year, 12, 31));
		}

		$this->template->events = $qb->getQuery()->getResult();
	}

	protected function createComponentFilterForm(): Form
	{
		$form = $this->formFactory->create();
		$form->setMethod('GET');

		/** @var Department[] $departments */
		$departments = $this->entityManager->getRepository(Department::class)->findBy([
			'manager' => $this->getUser()->getId(),
		]);
		$departmentsOptions = [];
		foreach ($departments as $department) {
			$departmentsOptions[$department->getId()] = $department->getCode();
		}
		$form->addSelect('department', 'Department', $departmentsOptions)
			->setPrompt('');

		$qb = $this->entityManager->createQueryBuilder();
		$qb->select('e');
		$qb->from(Event::class, 'e');
		$qb->where($qb->expr()->eq('e.manager', ':manager'));
		$qb->setParameter('manager', $this->getUser()->getId());
		$years = [];
		/** @var Event $event */
		foreach ($qb->getQuery()->getResult() as $event) {
			$years[$event->getDate()->format('Y')] = $event->getDate()->format('Y');
		}
		rsort($years);

		$form->addSelect('year', 'Year', $years);
		$form->addSubmit('filter', 'Filter');

		return $form;
	}
}
