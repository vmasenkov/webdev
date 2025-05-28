<?php
            // перевірка додавання броні
            //is_numeric($_GET['id']) or die("invalid URL");
            
            require_once '_db.php';
            
            $rooms = $db->query('SELECT * FROM rooms');
            
            $start = $_GET['start']; // ЗРОБИТИ правильне форматування
            $end = $_GET['end']; // ЗРОБИТИ правильне форматування
        ?>
        <form id="f_create" action="backend_create.php" method="post" style="padding:20px;" onsubmit="document.app.createReservation(event); return false;">
            <h1>New Reservation</h1>
            <div>Name: </div>
            <div><input type="text" id="name" name="name" value="" /></div>
            <div>Start:</div>
            <div><input type="text" id="start" name="start" value="<?php echo $start ?>" /></div>
            <div>End:</div>
            <div><input type="text" id="end" name="end" value="<?php echo $end ?>" /></div>
            <div>Room:</div>
            <div>
                <select id="room" name="room">
                    <?php 
                        foreach ($rooms as $room) {
                            $selected = $_GET['resource'] == $room['id'] ? ' selected="selected"' : '';
                            $id = $room['id'];
                            $name = $room['name'];
                            print "<option value='$id' $selected>$name</option>";
                        }
                    ?>
                </select>
                
            </div>
            <div class="space"><input type="submit" value="Save"/> </div>
        </form>
