<?php
header ( "Location: ./docs/" . (isset ( $_GET ['r'] ) ? $_GET ['r'] : 'index.html') );