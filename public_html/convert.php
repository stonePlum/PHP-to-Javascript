<?php
/**
 * Created by PhpStorm.
 * User: mostkaj
 * Date: 26.5.2016
 * Time: 20:48
 */
if (!isset($_COOKIE["converter"])) {
    return;
}
if (!@$_POST["code"]) {
    return;
}
$code = urldecode($_POST["code"]);

$change = [
    "__AND__" => "&",
    "__PLUS__" => "+",
    "__QUESTION_MARK__" => "?",
    "__ART__" => "alert",
    "__CA_SE__" => "case"
];

$code = str_replace(array_keys($change), array_values($change), $code);
require_once __DIR__ . "/../vendor/autoload.php";

try {

    $parser = (new \PhpParser\ParserFactory())->create(\PhpParser\ParserFactory::PREFER_PHP7);
    $jsPrinter = new \phptojs\JsPrinter\JsPrinter();

    $stmts = $parser->parse($code);
    ob_start();
    $jsCode = $jsPrinter->jsPrint($stmts);
    $errors = ob_get_clean();
    $errors = explode(PHP_EOL, $errors);
    foreach ($errors as $error) {
        if ($error != "") {
            echo "//" . $error;
        }
        echo PHP_EOL;
    }
    foreach ($jsPrinter->getErrors() as $error) {
        echo "//" . $error . PHP_EOL;
    }
    echo $jsCode;

} catch (PhpParser\Error $e) {
    echo 'ERROR:', $e->getMessage();
} catch (Exception $e) {
    echo "ERROR:Some is wrong".PHP_EOL.$e->getMessage();
}