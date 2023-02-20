<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<") ?>

[dashboard_list id=my-lists title='Moje listy przedmiotów' lists=$myLists selectable editable removable endpoint=list]

[dashboard_list id=other-lists title='Inne listy przedmiotów' lists=$otherLists selectable openable removable endpoint=contribution]