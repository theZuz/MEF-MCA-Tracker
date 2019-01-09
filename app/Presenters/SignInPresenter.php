<?php declare(strict_types=1);

namespace App\Presenters;

use App\Forms\FormFactory;
use App\Model\Manager;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Security\Identity;

final class SignInPresenter extends Presenter
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

	protected function createComponentForm(): Form
	{
		$form = $this->formFactory->create();
		$form->addEmail('email', 'Email')
			->setRequired();
		$form->addPassword('password', 'Password')
			->setRequired();
		$form->addSubmit('submit', 'Sign in');

		$form->onSuccess[] = function (Form $form) {
			$values = $form->getValues();
			/** @var Manager $manager */
			$manager = $this->entityManager->getRepository(Manager::class)->findOneBy([
				'email' => $values->email,
			]);;
			if (!$manager || !$manager->validatePassword($values->password)) {
				$this->flashMessage('Incorrect email or password');
				$this->redirect('this');
			}
			$this->getUser()->login(new Identity($manager->getId()));
			$this->redirect('Homepage:');
		};

		return $form;
	}
}
