# ZergMember
PHP Member Management System

## What is it?
>말 그대로 멤버 관리 시스템입니다. MySQLi를 이용한 회원가입과 로그인을 해야하는 부분이 필요할때 이 클래스를 사용하면 쉽게 해결할 수 있습니다.

## How to use?
>### Initalize Class

>>먼저, 클래스를 불러오는 방법은 간단합니다. 상단에 class.ZergMember.php를 참조시켜 주시고,

```php
$member = new ZergMember(<DB Host>, <DB Account>, <DB Password>, <DB Scheme>, <DB Table>);
```

>>위와 같은 방식으로 클래스를 선언하시면 됩니다.

## License
>ZergMember 클래스(password.php 포함)는 MIT License에 따라 사용하실 수 있습니다.
