<?php
            // перевірка додавання броні
            //is_numeric($_GET['id']) or die("invalid URL");
            
            require_once '_db.php';

            $rooms = $db->query('SELECT * FROM rooms');

            $query = $db->prepare('SELECT * FROM reservations WHERE id = :id');
            $query->bindParam(":id", $_GET["id"]);
            $query->execute();
            $reservation = $query->fetch(PDO::FETCH_ASSOC);

        ?>
        <form id="f_update" action="backend_update.php" method="post" style="padding:20px;" onsubmit="document.app.updateReservation(event); return false;">
            <h1>Edit Reservation</h1>
            <input type="hidden" id="id" name="id" value="<?php echo $reservation["id"] ?>" />
            <div>Name: </div>
            <div><input type="text" id="name" name="name" value="<?php echo $reservation["name"] ?>" /></div>
            <div>Start:</div>
            <div><input type="text" id="start" name="start" value="<?php echo $reservation["start"] ?>" /></div>
            <div>End:</div>
            <div><input type="text" id="end" name="end" value="<?php echo $reservation["end"] ?>" /></div>
            <div>Room:</div>
            <div>
                <select id="room" name="room">
                    <?php 
                        foreach ($rooms as $room) {
                            $selected = $reservation['room_id'] == $room['id'] ? ' selected="selected"' : '';
                            $id = $room['id'];
                            $name = $room['name'];
                            print "<option value='$id' $selected>$name</option>";
                        }
                    ?>
                </select>
                
            </div>
            <div>Status:</div>
            <div>
                <select id="status" name="status">
                    <?php 
                        $options = array("New", "Confirmed", "Arrived", "CheckedOut");
                        foreach ($options as $option) {
                            $selected = $option == $reservation['status'] ? ' selected="selected"' : '';
                            $id = $option;
                            $name = $option;
                            print "<option value='$id' $selected>$name</option>";
                        }
                    ?>
                </select>                
            </div>
            <div>Paid:</div>
            <div>
                <select id="paid" name="paid">
                    <?php 
                        $options = array(0, 50, 100);
                        foreach ($options as $option) {
                            $selected = $option == $reservation['paid'] ? ' selected="selected"' : '';
                            $id = $option;
                            $name = $option."%";
                            print "<option value='$id' $selected>$name</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="space"><input type="submit" value="Save" /></div>
        </form>