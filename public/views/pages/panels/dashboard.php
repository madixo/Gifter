<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<") ?>

[list id=my-lists title='Moje listy' addable=[value='listę', input=list_name] placeholder='Nazwa listy' items=$myLists data=[selectable, openable, editable, removable, endpoint=list, addable]]

[list id=other-lists title='Listy, w którch uczestniczę' addable=[value='listę', input=list_code] placeholder=Kod items=$otherLists data=[selectable, openable, removable, endpoint=contribution, addable]]