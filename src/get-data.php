<?php 
    require_once("conn.php");
    require_once("questions-map.php");

    $question_code = isset($_POST['question']) ? $question_code[$_POST['question']] : null;
    $answer = isset($_POST['answer']) ? $_POST['answer'] : null;

    $query = "SELECT strongly_disagree_count, disagree_count, neutral_count, agree_count, strongly_agree_count FROM responses_cache WHERE question_code=" . question_code . ";";
    try
        {
        $prepared_stmt = $dbo->prepare($query);
        $prepared_stmt->execute();
        $result = $prepared_stmt->fetchAll();
        echo json_encode($result);
    }
    catch (PDOException $ex)
    { 
      echo $sql . "<br>" . $error->getMessage();
    }
?>