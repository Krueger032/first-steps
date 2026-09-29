<?php

AddEventHandler("iblock", "OnBeforeIBlockElementDelete", Array("Itb\Event\Catalog", "OnBeforeIBlockElementDeleteHandler"));