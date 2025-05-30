
        <form id="f_room_create" action="backend_room_create.php" method="post" style="padding:20px;" onsubmit="document.app.createRoom(event); return false;">
            <h1>New Room</h1>
            <div>Name: </div>
            <div><input type="text" id="name" name="name" value="" /></div>
            <div>Capacity:</div>
            <div><input type="number" id="capacity" name="capacity" value="" /></div>
            <div class="space"><input type="submit" value="Save"/> </div>
        </form>
