<?

if (!defined('UP_ROOT')) {
	define('UP_ROOT', './');
}
require UP_ROOT.'functions.inc.php';

$out = <<<FMB
<div id="status">&nbsp;</div>
	<h2>О проекте</h2>

	<p><strong>АП</strong>&nbsp;&#151; файлообменник и&nbsp;хостинг файлов, он&nbsp;позволяет
	обмениваться файлами размером до&nbsp;$max_file_size&nbsp;мегабайт включительно. Файлы на&nbsp;файлообменнике хранятся пока их&nbsp;кто-то качает.
	По&nbsp;прошествии определенного времени с&nbsp;момента последнего скачивания, файл будет удален.
	Время, через которое файл будет удален, зависит от&nbsp;его размера.</p>

	<h3>Сроки хранения файлов</h3>
	<ul>
		<li>Файлы до $small_file_size&nbsp;мегабайт&nbsp;&#151; $non_downloaded_small_files_interval&nbsp;дней с&nbsp;момента последнего скачивания.</li>
		<li>Файлы размером, от&nbsp;$small_file_size&nbsp;мегабайт до&nbsp;$max_file_size&nbsp;мегабайт&nbsp;&#151; хранятся $non_downloaded_big_files_interval&nbsp;дней.</li>
		<li>Файлы, которые никто ниразу не&nbsp;скачивал, удаляются через $non_downloaded_interval&nbsp;дней после заливки.</li>
		<li>Популярные файлы (которые были скачаны больше $popular_num&nbsp;раз) хранятся $non_downloaded_big_files_popular_interval&nbsp;дней.</li>
		<li>Файлы, помеченные как «спам», хранятся 2 дня.</li>
	</ul>


	<h3></h3>
	<p>За содержимое файлов отвечают лишь те,&nbsp;кто заливал файл. Администрация никак не&nbsp;контролирует
	их&nbsp;содержимое и&nbsp;не&nbsp;выслушивает претензии по&nbsp;этому поводу. Файлы, не&nbsp;предназначеные
	для несовершенолетних лиц, настоятельно рекомендуется делать скрытыми.</p>

	<p>Логи с&nbsp;информацией о&nbsp;тех,
	кто заливал файлы или скачивал не существуют. Мы&nbsp;не&nbsp;поддерживаем пиратов, но&nbsp;уважаем право людей на&nbsp;анонимность.
	Однако файлы, которые нарушают авторские права, могут быть удалены, по&nbsp;требованию правообладателя.</p>

 	<p>Сервис предоставляется &laquo;as&nbsp;is&raquo;, администрация не&nbsp;может
	гарантировать работоспособность сервиса или сохранность файлов. Но&nbsp;мы&nbsp;делаем все,
	чтобы проблем с&nbsp;файлами не&nbsp;возникало.</p>
FMB;

printPage($out);

?>

