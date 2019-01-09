<?php declare(strict_types=1);

namespace App\Presenters;

use Nette\Application\UI\Presenter;

final class SignOutPresenter extends Presenter
{
	public function actionDefault(): void
	{
		$this->getUser()->logout();
		$this->redirect('SignIn:');
	}
}
