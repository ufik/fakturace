<?php

namespace Model;

use Nette,
	Nette\Utils\Strings;


/**
 * Users authenticator.
 */
class Authenticator extends Nette\Object implements Nette\Security\IAuthenticator
{
	/** @var Nette\Database\Connection */
	private $database;

	public function __construct(Nette\Database\Connection $database){
		$this->database = $database;
	}

	/**
	 * Performs an authentication.
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials){
		list($username, $password) = $credentials;
		$row = $this->database->table('users')->where('username', $username)->fetch();

		if (!$row) {
			throw new Nette\Security\AuthenticationException('Tento uživatel neexistuje.', self::IDENTITY_NOT_FOUND);
		}

		if ($row->password !== $this->calculateHash($password, $row->password)) {
			throw new Nette\Security\AuthenticationException('Nepodařilo se ověřit uživatele.', self::INVALID_CREDENTIAL);
		}

		unset($row->password);
		return new Nette\Security\Identity($row->id, $row->role, $row->toArray());
	}

	/**
	 * Computes salted password hash.
	 * @param  string
	 * @return string
	 */
	public static function calculateHash($password, $salt = NULL){
		if ($salt === null) {
	        $salt = '$2a$07$' . Nette\Utils\Strings::random(32) . '$';
	    }
	    
    	return crypt($password, $salt);
	}

}
