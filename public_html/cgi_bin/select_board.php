<html>
    <head>
    </head>
    <body>

        <form name="input" action="discussion_board.php" method="post" autocomplete="on">
            <!--Hidden field to determine whether form is register or login-->
            <input type="hidden" name="action" value="board_selection">
            <!-- Aligned text boxes-->
            <fieldset>
                <b id="boardname">Boardname:</b> <input type="text" name="boardname" required> <br />

            </fieldset>

            <!--Submit section-->
            <input type="submit" name="board_selection" value="Select">
        <?php
        // Anna Reining, 260885420
        // Displays a list of discussion boards and handles board 
        // related actions
        
        ?>

    </body>
</html>
