<?php

$dbuser = 'x';
$pass = 'x';

if (isset($_POST['action']) and $_POST['action'] == 'creategame')
{
  $pcount = 0;
  $parray = (isset( $_POST['players']) ) ? $_POST['players'] : NULL;
  for ($i = 0; $i < 6; $i++) {
    if ($parray[$i] == 'noplayer') {
      $parray[$i] = NULL;      
    } else {
      $pcount++;
    }
  }

  $DBH = new PDO('mysql:host=localhost;dbname=prod', $dbuser, $pass);
  $stmt = $DBH->prepare( 'INSERT INTO dsgamesrecord '
    . '(mammal, reptile, bird, amphibian, arachnid, insect, players) '
    . 'VALUES (?, ?, ?, ?, ?, ?, ?);' );
  if (!$stmt->execute( array( $parray[0], $parray[1], $parray[2], $parray[3], $parray[4], $parray[5], $pcount ) )) {
    exit ('INSERT failed');
  }
  $id = $DBH->lastInsertID();
  $stmt = $DBH->prepare( 'SELECT timestamp FROM dsgamesrecord WHERE id = ?' );
  $stmt->execute( array( $id ) );
  $rows = $stmt->fetchAll();

  exit (json_encode( array( $id, $rows[0]['timestamp'] ) ));
}

if (isset($_POST['action']) and $_POST['action'] == 'setwinner')
{
  $gameid = (isset($_POST['gameid'])) ? $_POST['gameid'] : 0;
  $winner = (isset( $_POST['winner']) ) ? $_POST['winner'] : 0;
  $winnerspecies = (isset( $_POST['winnerspecies']) ) ? $_POST['winnerspecies'] : 0;

  $DBH = new PDO('mysql:host=localhost;dbname=prod', $dbuser, $pass);
  $stmt = $DBH->prepare( 'UPDATE dsgamesrecord SET winnerplayer = ? WHERE id = ?;' );
  if (!$stmt->execute( array( $winner, $gameid ) )) {
    exit ('UPDATE winner failed');
  }
  $stmt = $DBH->prepare( 'UPDATE dsgamesrecord SET winnerspecies = ? WHERE id = ?;' );
  if (!$stmt->execute( array( $winnerspecies, $gameid ) )) {
    exit ('UPDATE winnerspecies failed');
  }
  exit ('setwinner success!');
}

if (isset($_GET['action']) and $_GET['action'] == 'getstats')
{
  $player = (isset($_GET['player'])) ? $_GET['player'] : 0;

  $DBH = new PDO('mysql:host=localhost;dbname=prod', $dbuser, $pass);
  $stmt = $DBH->prepare( 'SELECT COUNT(*) FROM dsgamesrecord WHERE winnerplayer = ?;' );
  if (!$stmt->execute( array( $player ) )) {
    exit ('GET wins failed');
  }
  $rows = $stmt->fetchAll();
  $wins = $rows[0][0];

  $stmt = $DBH->prepare( 'SELECT COUNT(*) FROM dsgamesrecord WHERE mammal = ?;' );
  if (!$stmt->execute( array( $player ) )) {
    exit ('GET player1 failed');
  }
  $rows = $stmt->fetchAll();
  $mammal = $rows[0][0];

  $stmt = $DBH->prepare( 'SELECT COUNT(*) FROM dsgamesrecord WHERE reptile = ?;' );
  if (!$stmt->execute( array( $player ) )) {
    exit ('GET player2 failed');
  }
  $rows = $stmt->fetchAll();
  $reptile = $rows[0][0];

  $stmt = $DBH->prepare( 'SELECT COUNT(*) FROM dsgamesrecord WHERE bird = ?;' );
  if (!$stmt->execute( array( $player ) )) {
    exit ('GET player3 failed');
  }
  $rows = $stmt->fetchAll();
  $bird = $rows[0][0];

  $stmt = $DBH->prepare( 'SELECT COUNT(*) FROM dsgamesrecord WHERE amphibian = ?;' );
  if (!$stmt->execute( array( $player ) )) {
    exit ('GET player4 failed');
  }
  $rows = $stmt->fetchAll();
  $amphibian = $rows[0][0];

  $stmt = $DBH->prepare( 'SELECT COUNT(*) FROM dsgamesrecord WHERE arachnid = ?;' );
  if (!$stmt->execute( array( $player ) )) {
    exit ('GET player5 failed');
  }
  $rows = $stmt->fetchAll();
  $arachnid = $rows[0][0];

  $stmt = $DBH->prepare( 'SELECT COUNT(*) FROM dsgamesrecord WHERE insect = ?;' );
  if (!$stmt->execute( array( $player ) )) {
    exit ('GET player6 failed');
  }
  $rows = $stmt->fetchAll();
  $insect = $rows[0][0];

  exit (json_encode( array( $wins, $mammal, $reptile, $bird, $amphibian, $arachnid, $insect ) ));
}

?>