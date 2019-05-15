<?php 
require_once('inc/functions.php');
$info = '';
$task = isset($_GET['task']) ? $_GET['task'] : 'report';
$error = isset($_GET['error']) ? $_GET['error'] : '0';

if ('delete' == $task) {
    $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
    if ($id>0) {
        deleteStudent($id);
        header('location: /crud/index.php?task=report');
    }
    
}

if ('seed' == $task) {
    seed();
    $info = 'Seed is complate';
}

$fname = '';
$lname = '';
$roll = '';
if (isset($_POST['submit'])) {
    $fname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_STRING);
    $lname = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_STRING);
    $roll = filter_input(INPUT_POST, 'roll', FILTER_SANITIZE_STRING);
    $id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);

    if ($id) {
        // Update the exiesting student
        if ($id !='' && $fname !='' && $lname !='' && $roll !='') {
            $result = updateStudent($id, $fname, $lname, $roll);

            if ($result) {
                header('location: /crud/index.php?task=report');
            } else{
                $error = "1";
            }
        }
        
    } else{
        // Add a new student
        if ($fname !='' && $lname !='' && $roll !='') {
            $result = addStudent($fname, $lname, $roll);
    
            if ($result) {
                header('location: /crud/index.php?task=report');
            } else{
                $error = "1";
            }
        }
    }

    

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CRUD Project</title>
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic">
    <link rel="stylesheet" href="//cdn.rawgit.com/necolas/normalize.css/master/normalize.css">
    <link rel="stylesheet" href="//cdn.rawgit.com/milligram/milligram/master/dist/milligram.min.css">
    
</head>
<body>
<div class="container">
    <div class="row">
        <div class="column column-60 column-offset-20">
            <h2>Project-2 CRUD</h2>
            <p>A simple project to perform CRUD operations using plan file and php</p>
            <?php include_once('inc/templates/nav.php'); ?>
            <br>
            <?php 
                if($info != ''){
                    echo $info;
                } 
            ?>

        </div>
    </div>
    <?php if ('1' == $error): ?>
        <div class="row">
            <div class="column column-60 column-offset-20">
                <blockquote>
                    Duplicate roll numbar!
                </blockquote>
            </div>
        </div>
    <?php endif; ?>
    <?php if ('report' == $task): ?>
        <div class="row">
            <div class="column column-60 column-offset-20">
                <?php generateReport(); ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if('add' == $task): ?>
        <div class="row">
            <div class="column column-60 column-offset-20">

                <form action="/crud/index.php?task=add" method="post">
                    <fieldset>
                        <label for="fname">First Name</label>
                        <input type="text" name="fname" id="fname" value="<?php echo $fname; ?>">
                        <label for="lname">Last Name</label>
                        <input type="text" name="lname" id="lname"  value="<?php echo $lname; ?>">
                        <label for="roll">Roll</label>
                        <input type="number" name="roll" id="roll"  value="<?php echo $roll; ?>">
                        
                        <input class="button-primary" type="submit" value="Save" name="submit">
                    </fieldset>
                </form>
                            
            </div>
        </div>
    <?php endif; ?>
    <?php 
    if('edit' == $task): 
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
        $student = getStudents($id);
        if($student):
        
    ?>
        <div class="row">
            <div class="column column-60 column-offset-20">

                <form method="post">
                    <fieldset>
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <label for="fname">First Name</label>
                        <input type="text" name="fname" id="fname" value="<?php echo $student['fname']; ?>">
                        <label for="lname">Last Name</label>
                        <input type="text" name="lname" id="lname"  value="<?php echo $student['lname']; ?>">
                        <label for="roll">Roll</label>
                        <input type="number" name="roll" id="roll"  value="<?php echo $student['roll']; ?>">
                        
                        <input class="button-primary" type="submit" value="Update" name="submit">
                    </fieldset>
                </form>
                            
            </div>
        </div>
<?php 
    endif;
endif; 
?>
</div>

    <script src="assets/js/script.js"></script>
</body>
</html>