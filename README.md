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
<p>В инфоблоке создаем элемент. В поле <b>символьный код (CODE)</b> прописываем адрес необходимой нам страницы. В поле <b>URL копия (PROPERTY_URL_COPY)</b> прописываем адрес страницы, окторую необходимо копировать.</p><br />
<p>На СЕО-странице используется глобальная переменная <b>$seoUrls</b>. В ней передается ID текущего элемента из инфоблока СЕО.</p><br />
<p>В настройках таблицы b_citfact_uservars можно выставить параметр <b>Редирект</b> в <b>"Y"</b>, тогда будет работать редирект из обычной страницы на сео-страницу</p>

<p>* Таблицы также используются в модуле <a href="https://github.com/studiofact/citfact.uservars" target="_blank"><b>citfact.uservars</b></a>.</p>