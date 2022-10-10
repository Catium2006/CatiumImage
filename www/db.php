<?php

function exec_sql($sql)
{
    $dbtype="mysql";
    $username = "web";
    $password = "web";
    $dbname = "CatiumImage";
    try {
        $conn = new PDO("$dbtype:host=localhost;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        // 设置结果集为关联数组
        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $arr = [];
        foreach (new RecursiveArrayIterator($stmt->fetchAll()) as $k => $v) {
            array_push($arr, $v);
        }
    } catch (PDOException $e) {
        echo '{"status":"failed","result":"' . $e->getMessage() . '"}<br>';
    }
    return $arr;
}

function getBackgroundImg()
{
    return exec_sql("SELECT background_img FROM mgmt WHERE id = 0")[0]['background_img'];
}
function getGridImg()
{
    return exec_sql("SELECT grid_img FROM mgmt WHERE id = 0")[0]['grid_img'];
}
function getNotice()
{
    return exec_sql("SELECT notice FROM mgmt WHERE id = 0")[0]['notice'];
}
function getCount()
{
    return exec_sql("SELECT file_count FROM mgmt WHERE id = 0")[0]['file_count'];
}
function getDomain(){
    return exec_sql("SELECT domain FROM mgmt WHERE id = 0")[0]['domain'];
}