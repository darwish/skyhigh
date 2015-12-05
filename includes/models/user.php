<?php

class Model_User extends RedBean_SimpleModel {

	public function update() {
		$this->validate();
	}

	protected function validate() {
		if (empty($this->bean->username)) {
			throw new ValidationException("Username cannot be empty.");
		}
		if (strlen($this->bean->password) < 2) {
			throw new ValidationException("Password must be at least 2 character. Come on.");
		}

		$existingUser = R::findOne('user', 'username = ? and id != ?', [$this->bean->username, $this->bean->id]);
		if ($existingUser) {
			throw new ValidationException("Username has already been taken.");
		}
	}

}