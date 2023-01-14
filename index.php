<?php
declare(strict_types=1);



$store = file_get_contents("./store");
$store = json_decode($store, true);
$state = $store["state"];

function draw(array $towers): string {
  $strTower = "[";
  foreach($towers as $towerIndex => $rings) {
    $strTower .= "[";
    for ($i = count($rings) - 1; $i >= 0; $i--) {
      $strTower .= $rings[$i] . ($i === 0 ? "" : ", ");
    }
    $strTower .= ($towerIndex === count($towers) - 1 ? "]" : "], ");
  }
  return $strTower . "]\n";
}

$isError = "false";


function deleteFirstRingByTowerNumber(array $state, int $from): array {
  $newTower = $state[$from];
  array_pop($newTower);
  $state[$from] = $newTower;
  return $state;
}

function returnFirstRing(array $state, int $from): int {
  $newTower = $state[$from];
  return array_pop($newTower);
}



function move(array $state, int $from, int $to): array {
  $newState = deleteFirstRingByTowerNumber($state, $from);
  $newState[$to][] = returnFirstRing($state, $from);
  return $newState;
}


function generateGameState(int $ringCount, int $towerCount, int $where): array{
  $state = [];
  for($i = 0; $i < $towerCount; $i++){
    $state[] = [];
  }
  for($i = $ringCount; $i >= 1; $i--){
    $state[$where][] = $i;
  }
  return $state;
}

$initialTowers = generateGameState(3, 3, 0);

function win(array $initialState, array $towers){
  $winState = $initialState;
  $firstTower = $winState[0];
  $winState[count($winState) - 1] = $firstTower;
  $winState[0] = [];

  return $towers === $winState;
}



function check(array $state, int $from, int $to): int {
  //var_dump($from);
  //var_dump($to);
  $a = $state[$from];
  $b = $state[$to];
  $ring1 = array_pop($a);
  $ring2 = array_pop($b);
  if (($ring1 === null && $ring2 === null) || ($ring1 === null && $ring2 !== null)) {
    return 1;
  }
  if($ring1 > $ring2 && $ring2 !== null){
    return 2;
  }

  return 3;
  

}


function run(array $state, int $from, int $to): array {
    $check = check($state, $from, $to);
    var_dump($check);
    if($check === 3){
        return move($state, $from, $to);
    }
    throw new Exception($check === 2 ? "Вы пытаетесь поставить большое кольцо на маленькое!!!" : "Такой ход совершить нельзя!!!");
}
if (isset($_POST['from']) && isset($_POST['to'])) {
    try {
        $state = run($state, (int)$_POST["from"] - 1, (int)$_POST['to'] - 1);
    } catch (Exception $exception) {
        $isError = "true";
        $errorMessage = $exception->getMessage();
    }
}

$win = win($initialTowers,$state);
var_dump($win);

if($win === true){
  $html = file_get_contents("./template.html");
  $state = generateGameState(3, 3, 0);
}else{
  $html = file_get_contents('./index.html');
}

$store["state"] = $state;
$store = json_encode($store);
file_put_contents("./store", $store);

echo preg_replace(
    [
        '/{{gameState}}/',
        '/{{showErrorMessage}}/',
        '/{{errorMessage}}/'
    ],
    [
        draw($state),
        $isError,
        $errorMessage,
    ],
    $html
);
