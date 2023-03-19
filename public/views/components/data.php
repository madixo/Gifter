<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<");

foreach($data ?? [] as $key => $value) {

    echo " data-$key";

    if(gettype($value) !== "boolean")
        echo "=\"$value\"";

}
