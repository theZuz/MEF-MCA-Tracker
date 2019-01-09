<?php declare(strict_types=1);

namespace App\Presenters;

use App\DataGrid\DataGridFactory;
use App\Model\Department;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Presenter;
use Ublaboo\DataGrid\DataGrid;

final class DepartmentListPresenter extends Presenter
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
		$qb->select('d');
		$qb->from(Department::class, 'd');
		$qb->where($qb->expr()->eq('d.manager', ':manager'));
		$qb->setParameter('manager', $this->getUser()->getId());

		$grid->setDataSource($qb);
		$grid->addColumnLink('code', 'Code', 'EmployeeList:', null, ['grid-filter[department]' => 'id']);
		$grid->addAction('remove', 'Remove', 'DepartmentRemove:')
			->setClass('btn btn-danger');

		return $grid;
	}
}
