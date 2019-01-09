<?php declare(strict_types=1);

namespace App\Presenters;

use App\Model\Event;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Presenter;

final class EventRemovePresenter extends Presenter
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
		/** @var Event $event */
		$event = $this->entityManager->getRepository(Event::class)->find($id);
		if ($event && $event->getManager()->getId() === $this->getUser()->getId()) {
			$this->entityManager->remove($event);
			$this->entityManager->flush();
		}
		$this->redirect('EventList:');
	}
}
