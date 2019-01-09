<?php declare(strict_types=1);

namespace App\Presenters;

use App\Model\Department;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Presenter;

final class DepartmentRemovePresenter extends Presenter
{
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;

	public function __construct(
		EntityManagerInterface $entityManager
	) {
		parent::__construct();
		$this->entityManager = $entityManager;
	}

	public function actionDefault(int $id): void
	{
		$department = $this->entityManager->getRepository(Department::class)->findOneBy([
			'id' => $id,
			'manager' => $this->getUser()->getId(),
		]);
		if ($department) {
			$this->entityManager->remove($department);
			$this->entityManager->flush();
		}
		$this->redirect('DepartmentList:');
	}
}
