<?php
/**
 * Member Management Class
 * 
 * 회원 관리 클래스
 * 
 * @author ZerglingGo <zerglinggo@zerglinggo.net>
 * @license http://opensource.org/licenses/MIT MIT License
 */
require_once("password.php");

class ZergMember {
	/**
	 * @var string Database Connection
	 */
	private $mysqli;
	/**
	 * @var string Database scheme name
	 */
	private $dbScheme;
	/**
	 * @var string Database table name
	 */
	private $dbTable;

	/**
	 * Constructor class
	 * 
	 * 생성자 클래스
	 * 
	 * @param string $host Enter database host
	 * @param string $dbid Enter database user identity
	 * @param string $dbpw Enter database user password
	 * @param string $scheme Enter database scheme name
	 * @param string $table Enter database table name
	 * 
	 */
	public function __construct($host, $dbid, $dbpw, $scheme, $table) {
		$this->mysqli = new mysqli($host, $dbid, $dbpw, $scheme);
		$this->dbScheme = $scheme;
		$this->setTable($table);
		$isExistsTable = $this->mysqli->query("SHOW TABLES LIKE '{$table}'")->num_rows == 1;
		if(!$isExistsTable) {
			$q = "CREATE TABLE `{$scheme}`.`{$table}` (  `no` INT NOT NULL AUTO_INCREMENT , ".
														"`id` VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL , ".
														"`pw` VARCHAR(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL , ".
														"`userdata` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL , ".
														"PRIMARY KEY (`no`)) ".
														"ENGINE = InnoDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT = 'ZergMember Members Table';";
			$this->mysqli->query($q);
		}

	}

	/**
	 * @param string $table set table name
	 */
	public function setTable($table) {
		$this->dbTable = $table;
	}

	/**
	 * @return string return to table name
	 */
	public function getTable() {
		return $this->dbTable;
	} 

	/**
	 * SQL Injection Prevention & HTML Tag Prevention Function
	 * 
	 * SQL 인젝션 방지 & HTML 태그 방지 함수
	 * 
	 * @param string $text
	 * @return string return to escaped string
	 */
	private function escape($text) {
		return htmlspecialchars($this->mysqli->real_escape_string($text));
	}

	/**
	 * User data save function
	 * 
	 * 유저 정보 저장 함수
	 * 
	 * @param string $id Enter user id
	 * @param string $key Enter data key
	 * @param string $value Enter data value
	 * @return mixed Return true when saved successfully. However if saving failure, Return the error information.
	 */
	public function setUserData($id, $key, $value) {
		if(!isset($id) || $id == "") {
			return "아이디를 입력해주세요."; // Return message "Please input the id."
		}
		if(!isset($key) || $key == "") {
			return "키를 입력해주세요."; // Return message "Please input the key."
		}
		if(!isset($value) || $value == "") {
			return "값을 입력해주세요."; // Return message "Please input the value."
		}
		$mysqli = $this->mysqli;
		$table = $this->dbTable;
		$escapedId = $this->escape(mb_strtolower($id)); // Call function to avoid SQL Injection.

		$q = "SELECT * FROM `{$table}` WHERE `id`='{$escapedId}'";
		$result = $mysqli->query($q);

		if($result->num_rows != 0) {
			$fetchAssoc = $result->fetch_assoc();
			$jsonUserData = $fetchAssoc["userdata"];
			$arrUserData = json_decode($jsonUserData, true);

			$arrUserData[$key] = $value;
			$jsonUserData = json_encode($arrUserData);
			$q = "UPDATE `{$table}` SET `userdata`='{$jsonUserData}' WHERE `id`='{$escapedId}'";
			$mysqli->query($q);
			return true;
		} else {
			return "존재하지 않는 계정입니다."; // Return message "The account does not exist."
		}
	}

	/**
	 * User data return function
	 * 
	 * 유저 정보 반환 함수
	 * 
	 * @param string $id Enter user id
	 * @param string $key Enter data key
	 * @return mixed Return data value if return successfully. However if return failure, Return the error information.
	 */
	public function getUserData($id, $key) {
		if(!isset($id) || $id == "") {
			return "아이디를 입력해주세요."; // Return message "Please input the id."
		}
		if(!isset($key) || $key == "") {
			return "키를 입력해주세요."; // Return message "Please input the key."
		}
		$mysqli = $this->mysqli;
		$table = $this->dbTable;
		$escapedId = $this->escape(mb_strtolower($id)); // Call function to avoid SQL Injection.

		$q = "SELECT * FROM `{$table}` WHERE `id`='{$escapedId}'";
		$result = $mysqli->query($q);

		if($result->num_rows != 0) {
			$fetchAssoc = $result->fetch_assoc();
			$jsonUserData = $fetchAssoc["userdata"];
			$arrUserData = json_decode($jsonUserData, true);
		} else {
			return "존재하지 않는 계정입니다."; // Return message "The account does not exist."
		}
		if(!isset($arrUserData[$key])) {
			return "존재하지 않는 키입니다."; // Return message "The key does not exist."
		}
		return $arrUserData[$key];
	}

	/**
	 * Change Password function
	 * 
	 * 비밀번호 변경 함수
	 * 
	 * @param string $id Enter member account identity
	 * @param string $pw Enter member account password
	 * @param string $newpw Enter member account new password
	 * @return mixed Return true when changed successfully
	 */
	public function changePassword($id, $pw, $newpw) {
		$mysqli = $this->mysqli;
		$table = $this->dbTable;

		$escapedId = $this->escape(mb_strtolower($id));
		$isLoginSuccess = $this->login($escapedId, $pw);
		if($isLoginSuccess === true) {
			$hashedNewPassword = password_hash($newpw, PASSWORD_BCRYPT);
			$q = "UPDATE `{$table}` SET `pw`='{$hashedNewPassword}' WHERE `id`='{$escapedId}'";
			$mysqli->query($q);
			return true;
		} else {
			return $isLoginSuccess;
		}
	}

	/**
	 * Register function
	 * 
	 * 회원 가입 함수
	 * 
	 * @param string $id Enter member account identity
	 * @param string $pw Enter member account password
	 * @param array $array OPTIONAL Enter member more details.
	 * @return mixed Return true if register successfully. However if register failure, Return the error information.
	 */
	public function register($id, $pw, $array = array("")) {
		$mysqli = $this->mysqli;
		$table = $this->dbTable;

		$escapedId = $this->escape(mb_strtolower($id)); // Call function to avoid SQL Injection.
		$hashedPassword = password_hash($pw, PASSWORD_BCRYPT);
		$json = json_encode($array);

		$q = "SELECT * FROM `{$table}` WHERE `id`='{$escapedId}'"; // Check exist ID
		if($mysqli->query($q)->num_rows == 0) {
			$q = "INSERT INTO `{$table}` (`id`, `pw`, `userdata`) VALUES ('{$escapedId}', '{$hashedPassword}', '{$json}')"; // Insert User Data
			$mysqli->query($q);
			return true;
		} else {
			return "이미 존재하는 아이디입니다."; // Return message "Already exist ID.";
		}
	}

	/**
	 * Login function
	 * 
	 * 로그인 함수
	 * 
	 * @param string $id Enter member account identity
	 * @param string $pw Enter member account password
	 * @return mixed Return true if login successfully. However if login failure, Return the error information.
	 */
	public function login($id, $pw) {
		$mysqli = $this->mysqli;
		$table = $this->dbTable;
		
		$escapedId = $this->escape(mb_strtolower($id)); // Call function to avoid SQL Injection.

		$q = "SELECT * FROM `{$table}` WHERE `id`='{$escapedId}'";
		$result = $mysqli->query($q);

		if($result->num_rows != 0) {
			$hashedPassword = $result->fetch_assoc()["pw"];
			$isLoginSuccess = password_verify($pw, $hashedPassword);
			if($isLoginSuccess) { // Check the password is correct.
				return true;
			} else {
				return "비밀번호가 틀립니다."; // Return message "Incorrect password."
			}
		} else {
			return "존재하지 않는 아이디입니다."; // Return message "The ID does not exist."
		}
	}
}
?>
