<?php declare(strict_types=1);

namespace App\Presenters;

use App\Forms\FormFactory;
use App\Model\Department;
use App\Model\Manager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

final class DepartmentCreatePresenter extends Presenter
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
		$form->addText('code', 'Code')->setRequired();
		$form->addSubmit('submit', 'Create');

		$form->onSuccess[] = function (Form $form) {
			$values = $form->getValues();

			$this->entityManager->persist(new Department(
				$this->entityManager->getRepository(Manager::class)->find($this->getUser()->getId()),
				$values->code
			));
			try {
				$this->entityManager->flush();
			} catch (UniqueConstraintViolationException $exception) {
				$this->flashMessage('Department with this code already exists.', 'danger');
				$this->redirect('this');
			}

			$this->flashMessage('Department successfully created', 'success');
			$this->redirect('DepartmentList:');
		};

		return $form;
	}
}
