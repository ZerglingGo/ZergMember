# ZergMember
PHP Member Management System

## What is it?
말 그대로 멤버 관리 시스템입니다. MySQLi를 이용한 회원가입과 로그인을 해야하는 부분이 필요할때 이 클래스를 사용하면 쉽게 해결할 수 있습니다.

## How to use?
### Initialize Class
먼저, 클래스를 불러오는 방법은 간단합니다. 상단에 class.ZergMember.php를 참조시켜 주시고,
```php
$zergmember = new ZergMember(<DB Host>, <DB Account>, <DB Password>, <DB Scheme>, <DB Table>);
```
위와 같은 방식으로 클래스를 선언하시면 됩니다.

### Member Registration
```php
$zergmember->register(<ID>, <Password>, [<User Data>]);
```
회원 가입 함수입니다. 아이디는 대소문자의 구분이 없습니다.<br />
마지막 옵션은 회원의 다른 정보를 저장할 수 있습니다. (나이, 별명 등..)<br />
마지막 옵션에 값을 넘길 때는 Array 형태로 넘기시면 됩니다.<br /><br />
반환 값이 있습니다. 회원 가입에 정상적으로 성공하면 true를 반환하고, 실패 시 오류 내용을 반환합니다.
```php
$userdata = array("age" => "20",
                  "nickname" => "ZerglingGo");
$result = $zergmember->register($id, $pw, $userdata);
if($result === true) {
  //Registration Success
} else {
  echo $result; // Error Text
}
```
지금까지 설명한 것을 코드로 짠다면 이런 식으로 하시면 됩니다.

### Member Login
```php
$zergmember->login(<ID>, <Password>);
```
로그인 함수입니다. 대소문자의 구분이 없습니다.<br /><br />
반환 값이 있습니다. 로그인에 정상적으로 성공하면 true를 반환하고, 실패 시 오류 내용을 반환합니다.
```php
$result = $zergmember->login($id, $pw);
if($result === true) {
  // Login Success
} else {
  echo $result; // Error Text
}
```

### Change Password
```php
$zergmember->changePassword(<ID>, <Password>, <New Password>);
```
비밀번호 변경 함수입니다.<br /><br />
반환 값이 있습니다. 비밀번호를 정상적으로 변경하면 true를 반환하고, 실패 시 오류 내용을 반환합니다.
```php
$result = $zergmember->changePassword($id, $pw, newpw);
if($result === true) {
  // Change Password Success
} else {
  echo $result; // Error Text
}
```

### Add Member Data
```php
$zergmember->setUserData(<ID>, <Key>, <Value>);
```
멤버의 정보를 추가하는 함수입니다.<br />
이것은 array 형태로 넣는것이 아닌 키, 값의 형태로 하나씩 입력합니다.<br /><br />
반환 값이 있습니다. 유저의 정보가 정상적으로 등록되면 true를 반환하고, 실패 시 오류 내용을 반환합니다.
```php
$result = $zergmember->setUserData($id, $key, $value);
if($result === true) {
  // Success
} else {
  echo $result; // Error Text
}
```

### Get Member Data
```php
$zergmember->getUserData(<ID>, <Key>, <Reference>);
```
멤버의 정보를 가져오는 함수입니다.<br />
멤버의 정보는 `<Reference>`로 지정한 변수에 담겨집니다.<br /><br />
반환 값이 있습니다. 유저의 키와 값을 정상적으로 가져오면 true를 반환하고, 실패 시 오류 내용을 반환합니다.
```php
$result = $zergmember->getUserData($id, $key, $data);
if($result === true) {
  echo $data;
} else {
  echo $result;
}
```
위와 같이 $data에 $key의 value가 담겨집니다.

## License
ZergMember 클래스(password.php 포함)는 MIT License에 따라 사용하실 수 있습니다.
