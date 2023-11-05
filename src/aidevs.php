<?php

use JetBrains\PhpStorm\NoReturn;

require_once "../vendor/autoload.php";
$dotenv = Dotenv\Dotenv::createImmutable('..');
$dotenv->load();

try{
    $tasksData = getTasksData();
}catch (Exception $e){
    scriptFailedMsg($e->getMessage());
}
if(!$tasksData){
scriptFailedMsg('Task data is missing. Check your tasks.json file.');
}

$tasksTable = [];
foreach ($tasksData as $taskObj){
    /** @var stdClass $taskObj */
        $tasksTable[$taskObj->number] = array_merge(
            (array)$taskObj,
            ['path' => sprintf('tasks/%s.php',$taskObj->name)]
        );
}

displayTasks($tasksTable);
$taskNumberToRun = getTaskNumberToRun();

if (isset($tasksTable[$taskNumberToRun]['path']) && file_exists($tasksTable[$taskNumberToRun]['path'])) {
    include $tasksTable[$taskNumberToRun]['path'];
} else {
   scriptFailedMsg("File not found in 'tasks' directory.");
}


/**
 * @throws Exception
 */
function getTasksData(){

    $tasksData = json_decode(file_get_contents('data/tasks.json'));

    if(property_exists($tasksData,'name') &&
        property_exists($tasksData,'number') &&
        property_exists($tasksData,'chapter') &&
        property_exists($tasksData,'lesson') &&
        property_exists($tasksData,'description')){
        throw new Exception('Some task data is missing, check your tasks.json and README.md file.');
    }

    return $tasksData->tasks ?? null;
}

#[NoReturn] function scriptFailedMsg(string $msg ): void
{
    echo "Script FAILED\n";
    echo "$msg\n";
    exit();
}

function displayTasks(array $taskArray){
    echo "___________________________\n";
    echo "ALL TASKS BELOW:          |\n";
    echo "===========================\n";

    foreach ($taskArray as $task) {

        $taskNumber = $task['number'] ?? '-';
        $taskChapter = $task['chapter'] ?? '-';
        $taskLesson = $task['lesson'] ?? '-';
        $taskName = $task['name'] ?? '-';

        printf('[%d] C0%dL0%d "%s"',$taskNumber,$taskChapter,$taskLesson,$taskName);
        echo "\n";

    }
    echo "===========================\n";

}

function getTaskNumberToRun(): int
{
    while (true){
        $input = readline('Enter the task number to run:');
        if(is_numeric($input) && ctype_digit($input)){
            return (int)$input;
        }else{
            echo "It's not a task number. Try again \n";
        }
    }

}

