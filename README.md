# СЕО-страницы (citfact.seopage)
<h3>Создаем СЕО-страницы подменой REQUEST_URI</h3>
<p>Модуль создает:</p>
<ul>
<li>тип инфоблока <b>Сервисы</b> (tools)</li>
<li>инфоблок <b>СЕО страницы</b> (SEOPAGES)</li>
<li>таблицы <b>b_citfact_uservars_group</b> и <b>b_citfact_uservars</b> и записи в них (*)</li>
<li>в корне сайта файл <b>fact_rewrite.php</b></li>
</ul>
<br />
<p>После установки необходимо вручную в файле .htaccess подменить строку<br />
<b>RewriteRule ^(.*)$ /bitrix/urlrewrite.php [L]</b><br />
на<br />
<b>RewriteRule ^(.*)$ /bitrix/fact_rewrite.php [L]</b></p><br />
<br />
<p>* Таблицы также используются в модуле <a href="https://github.com/studiofact/citfact.uservars" target="_blank"><b>citfact.uservars</b></a>.</p>