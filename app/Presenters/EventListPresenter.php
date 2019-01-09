<?php declare(strict_types=1);

namespace App\Presenters;

use App\DataGrid\DataGridFactory;
use App\Model\Department;
use App\Model\Event;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Presenter;
use Ublaboo\DataGrid\DataGrid;

final class EventListPresenter extends Presenter
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
		$qb->from(Event::class, 'e');
		$qb->leftJoin('e.departments', 'd');
		$qb->where($qb->expr()->eq('e.manager', ':manager'));
		$qb->setParameter('manager', $this->getUser()->getId());

		$grid->setDataSource($qb);

		$qb = $this->entityManager->createQueryBuilder();
		$qb->select('d.id', 'd.code');
		$qb->from(Department::class, 'd');
		$qb->where($qb->expr()->eq('d.manager', ':manager'));
		$qb->setParameter('manager', $this->getUser()->getId());
		$grid->addColumnText('department', 'Department', 'd.id')
			->setRenderer(function (Event $event) {
				$result = [];
				foreach ($event->getDepartments() as $department) {
					$result[] = $department->getCode();
				}

				return implode(', ', $result);
			})
			->setFilterSelect(array_column($qb->getQuery()->getResult(), 'code', 'id'))
			->setPrompt('');
		$grid->addColumnText('name', 'Name')
			->setFilterText();
		$grid->addColumnDateTime('date', 'Date')
			->setFilterText();
		$grid->addColumnNumber('price', 'Price $')
			->setFilterText();
		$grid->addColumnText('description', 'Description')
			->setFilterText();
		$grid->addAction('edit', 'Edit', 'EventEdit:');
		$grid->addAction('remove', 'Remove', 'EventRemove:')
			->setClass('btn btn-danger');

		return $grid;
	}
}
