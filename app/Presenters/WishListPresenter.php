<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette, Nette\Application\UI\Form;


class WishListPresenter extends BasePresenter
{
	private $database;

	public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}


	/**
	 * create
	 */
	public function renderCreate(): void
	{
	}


	/**
	 * show
	 */
	public function renderShow($ownerId): void
	{
		if (isset($ownerId)) {
			$this->template->ownerId = $ownerId;
			$this->template->wishes = $this->database->table('wishes')->where('owner_id = ?', $ownerId);
		} else {
			// $this->template->wishes = array();
		}
	}


	/**
	 * edit
	 */
	public function renderEdit($ownerId): void
	{
		if (isset($ownerId)) {
			$this->template->ownerId = (string)$ownerId;
			$wishes = $this->database->table('wishes')->where('owner_id = ?', $ownerId);

			$defaults = array();
			$defaults['ownerId'] = $ownerId;
			foreach ($wishes as $id => $row) {
				$defaults['id' . ($id + 1)] = $row['id'];
				$defaults['wish' . ($id + 1)] = $row['description'];
			}
			$this['wishListForm']->setDefaults($defaults);
		}
	}


	/**
	 * control newWishListForm
	 */
	protected function createComponentNewWishListForm(): Form
	{
		$form = new Form;

		$form->addText('wish1', 'První přání:');
		$form->addText('wish2', 'Druhé přání:');
		$form->addText('wish3', 'Třetí přání:');

		$form->addSubmit('send', 'Pokračovat');
		$form->onSuccess[] = [$this, 'insertNewWishList'];

		return $form;
	}

	/**
	 * save new wishList to database
	 */
	public function insertNewWishList(Form $form, \stdClass $values): void
	{
		$ownerId = rand(1, 4294967295); // new owner

		$this->database->table('wishes')->insert([
			'id' => rand(1, 4294967295), // random unsigned integer
			'owner_id' => $ownerId,
			'description' => $values->wish1
		]);
		$this->database->table('wishes')->insert([
			'id' => rand(1, 4294967295), // random unsigned integer
			'owner_id' => $ownerId,
			'description' => $values->wish2
		]);
		$this->database->table('wishes')->insert([
			'id' => rand(1, 4294967295), // random unsigned integer
			'owner_id' => $ownerId,
			'description' => $values->wish3
		]);
		// $this->flashMessage('Děkuji', 'success');
		$this->flashMessage('Děkuji [./show?ownerId='.$ownerId.'|Wishlist]', 'success');
		$this->redirect('this');
	}


	/**
	 * control wishListForm
	 */
	protected function createComponentWishListForm(): Form
	{
		$form = new Form;

		$form->addHidden('ownerId');

		$form->addHidden('id1');
		$form->addText('wish1', 'První přání:');

		$form->addHidden('id2');
		$form->addText('wish2', 'Druhé přání:');

		$form->addHidden('id3');
		$form->addText('wish3', 'Třetí přání:');

		$form->addSubmit('send', 'Pokračovat');
		$form->onSuccess[] = [$this, 'updateWishList'];

		return $form;
	}

	/**
	 * save existing wishList to database
	 */
	public function updateWishList(Form $form, \stdClass $values): void
	{
		$ownerId = $values->ownerId;

		$this->database->table('wishes')->where('id = ?', $values->id1)->update([
			'description' => $values->wish1
		]);
		$this->database->table('wishes')->where('id = ?', $values->id2)->update([
			'description' => $values->wish2
		]);
		$this->database->table('wishes')->where('id = ?', $values->id3)->update([
			'description' => $values->wish3
		]);
		$this->flashMessage('Děkuji', 'success');
		$this->redirect('this');
	}

}

