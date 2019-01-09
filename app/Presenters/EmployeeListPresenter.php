<?php declare(strict_types=1);

namespace App\Presenters;

use App\DataGrid\DataGridFactory;
use App\Model\Department;
use App\Model\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Presenter;
use Ublaboo\DataGrid\DataGrid;

final class EmployeeListPresenter extends Presenter
{
	/**
	 * @var DataGridFactory
	 */
	private $dataGridFactory;

	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	public function __construct(
		DataGridFactory $dataGridFactory,
		EntityManagerInterface $entityManager
	) {
		parent::__construct();
		$this->dataGridFactory = $dataGridFactory;
		$this->entityManager = $entityManager;
	}

	public function actionDefault(): void
	{
		if (!$this->getUser()->isLoggedIn()) {
			$this->redirect('SignIn:');
		}
	}

	protected function createComponentGrid(): DataGrid
	{
		$grid = $this->dataGridFactory->create();

		$qb = $this->entityManager->createQueryBuilder();
		$qb->select('e');
		$qb->from(Employee::class, 'e');
		$qb->join('e.department', 'd');
		$qb->where($qb->expr()->eq('d.manager', ':manager'));
		$qb->setParameter('manager', $this->getUser()->getId());

		$grid->setDataSource($qb);

		$qb = $this->entityManager->createQueryBuilder();
		$qb->select('d.id', 'd.code');
		$qb->from(Department::class, 'd');
		$qb->where($qb->expr()->eq('d.manager', ':manager'));
		$qb->setParameter('manager', $this->getUser()->getId());
		$grid->addColumnText('department', 'Department')
			->setRenderer(function (Employee $employee) {
				return $employee->getDepartment()->getCode();
			})
			->setFilterSelect(array_column($qb->getQuery()->getResult(), 'code', 'id'))
			->setPrompt('');
		$grid->addColumnText('cnum', 'CNUM')
			->setSortable()
			->setFilterText();
		$grid->addColumnText('name', 'Name')
			->setSortable()
			->setFilterText();
		$grid->addColumnText('country', 'Country')
			->setSortable()
			->setFilterText();
		$grid->addColumnDateTime('startDate', 'Start date')
			->setSortable()
			->setFilterDateRange();
		$grid->addColumnNumber('budget', 'Budget $');
		$grid->addAction('remove', 'Remove', 'EmployeeRemove:')
			->setClass('btn btn-danger');

		return $grid;
	}
}
