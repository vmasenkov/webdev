<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>HTML5 Бронювання кімнат в готелі (JavaScript/PHP/MySQL)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style-lab8.css" type="text/css" />
    <!-- допоміжні бібліотеки -->
    <script src="js/jquery-3.7.1.min.js" type="text/javascript"></script>
    <!-- бібліотека daypilot -->
    <script src="js/daypilot-all.min.js" type="text/javascript"></script>
</head>
<body>
  <header>
    <div class="container bg-help">
      <div class="inBox">
        <h1 id="logo">HTML5 Бронювання кімнат в готелі (JavaScript/PHP)</h1>
        <p id="claim">AJAX'овий Календар-застосунок з JavaScript/HTML5/jQuery</p>
        <hr class="hidden" />
      </div>
    </div>
  </header>
  <main role="main">
    <div class="container mb-3">
      <div class="d-flex">
        <div class="col-3">
          <label for="filter" class="form-label">Фільтр за кількістю ліжок:</label>
          <select id="filter" class="form-select">
            <option value="0">all</option>
            <option value="2">2 bads</option>
            <option value="3">3 bads</option>
            <option value="4">4 bads</option>
        </select>
        </div>
        <div class="ms-auto align-self-end">
          <button class="btn btn-primary" id="newRoomBtn">New room</button>
        </div>
      </div>
    </div>
    <div class="container mb-3">
      <div class="row">
        <div class="col-12">
          <h2>Календар бронювання</h2>
          <div id="dp"></div>
        </div>
      </div>
    </div>
  </main>
  <div class="clear">
  </div>
 <footer>
  <address class="container">(с)Автор лабораторної роботи: студент спеціальності ПЗІС, Масенков Владислав Ігорович</address>
 </footer>

 <script type="text/javascript">
  $(document).ready(function () {
  // Ініціалізація календаря
    var dp = new DayPilot.Scheduler("dp", {
      startDate: new DayPilot.Date().firstDayOfMonth(),
      days: 365,
      scale: "Day",
      timeHeaders: [
          {groupBy: "Month", format: "MMMM yyyy"},
          {groupBy: "Day", format: "d"}
      ],
      rowHeaderColumns: [
        {text: "Room", width: 80, display: "name"},
        {text: "Capacity", width: 80, display: "capacity"},
        {text: "Status", width: 80, display: "status"}
      ],
      treeEnabled: true,
      treePreventParentUsage: true,
      heightSpec: "Max",
      eventMovingStartEndEnabled: false,
      eventResizingStartEndEnabled: false,
      timeRangeSelectingStartEndEnabled: true,
      allowEventOverlap: false,
      onBeforeCellRender: args => {
          if (args.cell.start < new DayPilot.Date()) {
              args.cell.disabled = true;
              args.cell.backColor = "#ccc";
          }
      },
      onBeforeResHeaderRender: function (args) {
        var beds = function(count) {
            return count + " bed" + (count > 1 ? "s" : "");
        };
        args.resource.columns[1].html = beds(args.resource.capacity);
        switch (args.resource.status) {
            case "Dirty":
                args.resource.cssClass = "status_dirty";
                break;
            case "Cleanup":
                args.resource.cssClass = "status_cleanup";
                break;
        }
      },
      onBeforeEventRender: function (args) {
        var start = new DayPilot.Date(args.e.start);
        var end = new DayPilot.Date(args.e.end);

        var today = DayPilot.Date.today();
        var now = new DayPilot.Date();

        args.e.html = "<div class='row'><div class='col-12'>" + args.e.text + " (" + start.toString("M/d/yyyy") + " - " + end.toString("M/d/yyyy") + ")" + "</div>" ;

        switch (args.e.status) {
            case "New":
                var in2days = today.addDays(1);

                if (start < in2days) {
                    args.e.barColor = 'red';
                    args.e.toolTip = 'Застаріле (не підтверджено вчасно)';
                }
                else {
                    args.e.barColor = 'orange';
                    args.e.toolTip = 'Новий';
                }
                break;
            case "Confirmed":
                var arrivalDeadline = today.addHours(18);

                if (start < today || (start.getDatePart() === today.getDatePart() && now > arrivalDeadline)) { // must arrive before 6 pm
                    args.e.barColor = "#f41616";  // red
                    args.e.toolTip = 'Пізнє прибуття';
                }
                else {
                    args.e.barColor = "green";
                    args.e.toolTip = "Підтверджено";
                }
                break;
            case 'Arrived': // arrived
                var checkoutDeadline = today.addHours(10);

                if (end < today || (end.getDatePart() === today.getDatePart() && now > checkoutDeadline)) { // must checkout before 10 am
                    args.e.barColor = "#f41616";  // червоний
                    args.e.toolTip = "Пізній виїзд";
                }
                else
                {
                    args.e.barColor = "#1691f4";  // блакитний
                    args.e.toolTip = "Прибув";
                }
                break;
            case 'CheckedOut': // перевірено
                args.e.barColor = "gray";
                args.e.toolTip = "Перевірено";
                break;
            default:
                args.e.toolTip = "Невизначений стан";
                break;
        }

        args.e.html = args.e.html + "<div class='col-12' style='color:gray'>" + args.e.toolTip + "</div></div>";

        var paid = args.e.paid;
        var paidColor = "#aaaaaa";

        args.e.areas = [
            { bottom: 10, right: 4, html: "<div style='color:" + paidColor + "; font-size: 8pt;'>Paid: " + paid + "%</div>", v: "Visible"},
            { left: 4, bottom: 8, right: 4, height: 2, html: "<div style='background-color:" + paidColor + "; height: 100%; width:" + paid + "%'></div>", v: "Visible" }
        ];
      },
      contextMenu: new DayPilot.Menu({
          items: [
              {
                  text: "Edit",
                  onClick: (args) => {
                    $.ajax({
                        url: "edit.php?id=" + args.source.data.id,
                        method: "GET",
                        dataType: "html",
                        success: (data) => {
                            var modal = new DayPilot.Modal({
                              useIframe: false,
                            })
                            modal.showHtml(data)
                        },
                        error: (xhr, status, error) => {
                            dp.message("Error loading edit html:", error);
                        }
                    });
                  }
              },
              {
                  text: "Delete",
                  onClick: (args) => {
                    $.ajax({
                        url: "backend_delete.php",
                        method: "POST",
                        data: {
                          id: args.source.data.id
                        },
                        dataType: "json",
                        success: (data) => {
                            if (data.result === "OK") {
                                dp.message("Event deleted successfully.");
                                dp.events.remove(args.source);
                            } else {
                                dp.message("Error deleting event: " + data.message);
                            }
                        },
                        error: (xhr, status, error) => {
                            dp.message("Error loading edit html:", error);
                        }
                    });
                  }
              }
          ]
      }),
      onTimeRangeSelected: async (args) => {
          $.ajax({
              url: "new.php?start=" + args.start.toString() + "&end=" + args.end.toString() + "&resource=" + args.resource,
              method: "GET",
              dataType: "html",
              success: (data) => {
                  var modal = new DayPilot.Modal({
                    useIframe: false,
                  })
                  modal.showHtml(data)
              },
              error: (xhr, status, error) => {
                  dp.message("Error loading new html:", error);
              }
          });
      },
      onEventMoved: async (args) => {
          $.ajax({
              url: "backend_move.php",
              method: "POST",
              data: {
                  id: args.e.id,
                  newStart: args.newStart.toString(),
                  newEnd: args.newEnd.toString(),
                  room: args.newResource
              },
              dataType: "json",
              success: (data) => {
                  if (data.result === "OK") {
                      dp.message("Event moved successfully.");
                      document.app.loadData();
                  } else {
                      dp.message("Error moving event: " + data.message);
                  }
              },
              error: (xhr, status, error) => {
                  dp.message("Error moving event:", error);
              }
          });
      }
    });
    dp.init();

    document.app = {
      barColor(i) {
          const colors = ["#3c78d8", "#6aa84f", "#f1c232", "#cc0000"];
          return colors[i % 4];
      },
      barBackColor(i) {
          const colors = ["#a4c2f4", "#b6d7a8", "#ffe599", "#ea9999"];
          return colors[i % 4];
      },
      updateResources(data) {
          const resources = data.map((room) => ({
              id: room.id,
              name: room.name,
              capacity: room.capacity,
              status: room.status
          }));
          dp.update({resources});
      },
      updateEvents(data) {
          const events = data.map((event) => ({
              id: event.id,
              name: event.name,
              text: event.text,
              resource: event.resource,
              status: event.status,
              paid: event.paid,
              start: new DayPilot.Date(event.start),
              end: new DayPilot.Date(event.end),
              bubbleHtml: event.bubbleHtml,
              height: 70
          }));
          dp.update({events});
      },
      loadData() {
        $.ajax({
            url: "backend_rooms.php",
            method: "POST",
            data: {
                capacity: $("#filter").val()
            },
            dataType: "json",

            success: (data) => {
                this.updateResources(data);
            },
            error: (xhr, status, error) => {
                dp.message("Error loading resources:", error);
            }
        });

        var duedate = new Date(new Date());
        duedate.setDate(new Date().getDate() + 365)

        $.ajax({
            url: "backend_events.php",
            method: "POST",
            data: {
                start: dp.startDate.toString(),
                end: duedate.toISOString()
            },
            dataType: "json",
            success: (data) => {
                this.updateEvents(data);
            },
            error: (xhr, status, error) => {
                dp.message("Error loading events:", error);
            }
        });
      },
      createReservation (event) {
        event.preventDefault();
        
        var name = $("#f_create #name").val();
        var start = $("#f_create #start").val();
        var end = $("#f_create #end").val();
        var room = $("#f_create #room").val();

        $.ajax({
            url: 'backend_create.php',
            type: 'POST',
            data: {
                name: name,
                start: start,
                end: end,
                room: room  
            },
            dataType: 'json',
            success: function(response) {
                if (response.result == 'OK') {
                    dp.message('Reservation created successfully with ID: ' + response.id);
                    document.app.loadData(); // Reload data to reflect the new reservation
                    DayPilot.Modal.close();
                } else {
                    dp.message('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                dp.message('An error occurred: ' + error);
            }
        });
      },
      updateReservation (event) {
        event.preventDefault();

        var name = $("#f_update #name").val();
        var start = $("#f_update #start").val();
        var end = $("#f_update #end").val();
        var room = $("#f_update #room").val();
        var id = $("#f_update #id").val();
        var status = $("#f_update #status").val();
        var paid = $("#f_update #paid").val();

        $.ajax({
            url: 'backend_update.php',
            type: 'POST',
            data: {
                name: name,
                start: start,
                end: end,
                room: room,
                id: id,
                status: status,
                paid: paid
            },
            dataType: 'json',
            success: function(response) {
                if (response.result == 'OK') {
                    dp.message('Reservation updated successfully: ' + response.message);
                    document.app.loadData(); // Reload data to reflect the new reservation
                    DayPilot.Modal.close();
                } else {
                    dp.message('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                dp.message('An error occurred: ' + error);
            }
        });
      },
      createRoom (event) {
        event.preventDefault();
        
        var name = $("#f_room_create #name").val();
        var capacity = $("#f_room_create #capacity").val();

        $.ajax({
            url: 'backend_room_create.php',
            type: 'POST',
            data: {
                name: name,
                capacity: capacity  
            },
            dataType: 'json',
            success: function(response) {
                if (response.result == 'OK') {
                    dp.message('Room created successfully with ID: ' + response.id);
                    document.app.loadData(); // Reload data to reflect the new reservation
                    DayPilot.Modal.close();
                } else {
                    dp.message('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                dp.message('An error occurred: ' + error);
            }
        });
      },
    };
    document.app.loadData();

    $("#filter").change(function() {
        document.app.loadData();
    });
    $("#newRoomBtn").click(function() {
        $.ajax({
              url: "room_new.php",
              method: "GET",
              dataType: "html",
              success: (data) => {
                  var modal = new DayPilot.Modal({
                    useIframe: false,
                  })
                  modal.showHtml(data)
              },
              error: (xhr, status, error) => {
                  dp.message("Error loading new html:", error);
              }
        });
    });
  });
</script>
<script type="text/javascript">
  
</script>
</body>
</html>