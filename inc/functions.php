<?php 
define('DB_NAME', getcwd().'/data/db.txt');

function seed(){
    $data = array(
        array(
            'id'    => 1,
            'fname' => 'Kamal',
            'lname' => 'Ahamed',
            'roll'  => '12',
        ),
        array(
            'id'    => 2,
            'fname' => 'Jamale',
            'lname' => 'Ahamed',
            'roll'  => '9',
        ),
        array(
            'id'    => 3,
            'fname' => 'Ripon',
            'lname' => 'Ahamed',
            'roll'  => '8',
        ),
        array(
            'id'    => 4,
            'fname' => 'Nikhil',
            'lname' => 'Chondro',
            'roll'  => '7',
        ),
        array(
            'id'    => 5,
            'fname' => 'Jone',
            'lname' => 'Rozard',
            'roll'  => '6',
        )
    );
    $serializeData = serialize($data);
    file_put_contents(DB_NAME, $serializeData, LOCK_EX);

}

function generateReport(){
    $serializeData = file_get_contents(DB_NAME);
    $students = unserialize($serializeData);
    ?>
        <table>
            <tr>
                <th>Name</th>
                <th>Roll</th>
                <th width="25%">Action</th>
            </tr>
            <?php foreach($students as $student): ?>
                <tr>
                    <td><?php echo $student['fname']." ".$student['lname']; ?></td>
                    <td><?php echo $student['roll'] ?></td>
                    <td><a href="/crud/index.php?task=edit&id=<?php echo $student['id']; ?>">Edit</a> | <a class="delete" href="/crud/index.php?task=delete&id=<?php echo $student['id']; ?>">Delete</a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php 
}

function addStudent($fname, $lname, $roll){
    $found = false;
    $serializeData = file_get_contents(DB_NAME);
    $students = unserialize($serializeData);

    foreach ($students as $_student) {
        if($_student['roll'] == $roll){
            $found = true;
            break;
        }
    }

    if (!$found) {
        $newId = getNewId($students);
        $student = array(
            'id'    => $newId,
            'fname' => $fname,
            'lname' => $lname,
            'roll'  => $roll,
        );
        array_push($students, $student);
        $serializeData = serialize($students);
        file_put_contents(DB_NAME, $serializeData, LOCK_EX);
        return true;
    }
    return false;
}

function getStudents($id){
    $serializeData = file_get_contents(DB_NAME);
    $students = unserialize($serializeData);
    foreach ($students as $student) {
        if($student['id'] == $id){
            return $student;
        }
    }
    return false;
}

function updateStudent($id, $fname, $lname, $roll){
    $found = false;
    $serializeData = file_get_contents(DB_NAME);
    $students = unserialize($serializeData);

    foreach ($students as $_student) {
        if($_student['roll'] == $roll){
            $found = true;
            break;
        }
    }

    if(!$found){
        $students[$id-1]['fname'] = $fname;
        $students[$id-1]['lname'] = $lname;
        $students[$id-1]['roll']  = $roll;
        $serializeData            = serialize($students);
        file_put_contents(DB_NAME, $serializeData, LOCK_EX); 
        return true;
    }
    return false; 
}

function deleteStudent($id){
    $serializeData = file_get_contents(DB_NAME);
    $students      = unserialize($serializeData);

    foreach ($students as $ofset => $student) {
        if($student['id'] == $id){
            unset($students[$ofset]);
        }
    }

    $serializeData = serialize($students);
    file_put_contents(DB_NAME, $serializeData, LOCK_EX);
}

function printRaw(){
    $serializeData = file_get_contents(DB_NAME);
    $students      = unserialize($serializeData);
    print_r($students);
}

function getNewId($students){
    $maxId = max(array_column($students, 'id'));
    return $maxId+1;
}

/**
 * Data importing from CSV
 */
function processImportData($preData){
    $serializeData = file_get_contents(DB_NAME);
    $students      = unserialize($serializeData);

    // Upload csv file 
    $uploadsDir = getcwd().'/data';
    $tempName   = $preData['tmp_name'];
    $error      = $preData['error'];
    $newName    = 'predata.csv';

    if ($error == UPLOAD_ERR_OK) {
        move_uploaded_file($tempName, "$uploadsDir/$newName");
    }
    

    // Process and read csv file
    $preDataFile = getcwd().'/data/predata.csv';
    $pfp = fopen($preDataFile, "r");

    $preDataArray = array();
    $i=1;
    while ($line = fgetcsv($pfp)) {
        $sequence = array('id', 'fname', 'lname', 'roll');
        $setId    = array_unshift($line, $i);
        $nLine    = array_combine($sequence, $line);
        array_push($preDataArray, $nLine);   

        $i++;
    }

    // Write csv data in file
    $serializeData = serialize($preDataArray);
    file_put_contents(DB_NAME, $serializeData, LOCK_EX);

}