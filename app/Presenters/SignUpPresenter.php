<?php declare(strict_types=1);

namespace App\Presenters;

use App\Forms\FormFactory;
use App\Model\Manager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

final class SignUpPresenter extends Presenter
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
		$form->addPassword('passwordAgain', 'Password again')
			->setRequired()
			->addRule(Form::EQUAL, 'Passwords do not match.', $form['password']);
		$form->addSubmit('submit', 'Sign up');

		$form->onSuccess[] = function (Form $form) {
			$values = $form->getValues();
			$this->entityManager->persist(new Manager(
				$values->email,
				$values->password
			));
			try {
				$this->entityManager->flush();
			} catch (UniqueConstraintViolationException $exception) {
				$this->flashMessage('This email is already registered.', 'danger');
				$this->redirect('this');
			}

			$this->flashMessage('Sign up successful. Please sign in.', 'success');
			$this->redirect('Homepage:');
		};

		return $form;
	}
}
