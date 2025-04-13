<p>Успешная авторизация {{ date('d.m.Y, H:i') }}</p>
<ul>
    <li>Аккаунт: {{ $user->name }}</li>
    <li>Пользователь: {{ $user->name }}</li>
    <li>Логин: {{ $user->username }}</li>
    <li>E-mail: {{ $user->email }}</li>
    <li>IP: {{ $ip }}</li>
    <li>Netname: {{ $netname }}</li>
</ul>
