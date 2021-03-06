<?php

use PHPUnit\Framework\TestCase;

class GatewaysStudentBookingTest extends TestCase
{

    private $db;
    private $gatewayStudentBooking;
    private $idStudent;

//test FindStudentLessons-----------------------------------------------------------------------------------------
    public function testFindStudentLessonsFound()
    {
        $this->db = new SQLite3("./tests/dbForTesting2.db");
        $this->updateDates();
        $this->id = 7;
        $this->gatewayStudentBooking = new Server\api\GatewaysStudentBooking($this->db);
        // $exceptedData = array();
        $dataExcepted = array();
        $result = $this->gatewayStudentBooking->findStudentLessons($this->id);
        $this->assertIsArray($result);
    }

    public function testFindStudentLessonsNotFound()
    {
        $this->db = new SQLite3("./tests/dbForTesting.db");
        $this->id = 7;
        $this->gatewayStudentBooking = new Server\api\GatewaysStudentBooking($this->db);
        $dataExcepted = array();
        $result = $this->gatewayStudentBooking->findStudentLessons($this->id);
        //print_r($result);
        $this->assertEquals(0, $result);
    }
//-----------------------------------------------------------------------------------------------------------------------

//test FindWaitingLesson--------------------------------------------------------------------------------------------------
    public function testFindWaitingLessonFound()
    {
        $this->db = new SQLite3("./tests/dbWaiting.db");
        $this->restoreValues();

        $this->id = 900001;
        $this->gatewayStudentBooking = new Server\api\GatewaysStudentBooking($this->db);
        $result = $this->gatewayStudentBooking->findStudentWaitingLessons($this->id);
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function testFindWaitingLessonNotFound()
    {
        $this->db = new SQLite3("./tests/dbWaiting.db");
        $this->restoreValues();

        $this->id = 900000;
        $this->gatewayStudentBooking = new Server\api\GatewaysStudentBooking($this->db);
        $result = $this->gatewayStudentBooking->findStudentWaitingLessons($this->id);
        $this->assertEquals(0, $result);
    }
//------------------------------------------------------------------------------------------------------------------------------

//test FindBookedLesson--------------------------------------------------------------------------------------------------
    public function testFindBookedLessonFound()
    {
        $this->db = new SQLite3("./tests/dbForTesting2.db");
        $this->updateDates();
        $this->id = 7;
        $this->gatewayStudentBooking = new Server\api\GatewaysStudentBooking($this->db);
        $result = $this->gatewayStudentBooking->findStudentBookedLessons($this->id);
        $this->assertIsArray($result);
    }

    public function testFindBookedLessonNotFound()
    {
        $this->db = new SQLite3("./tests/dbForTesting.db");
        $this->id = 7;
        $this->gatewayStudentBooking = new Server\api\GatewaysStudentBooking($this->db);
        $result = $this->gatewayStudentBooking->findStudentBookedLessons($this->id);
        $this->assertEquals(0, $result);
    }
//------------------------------------------------------------------------------------------------------------------------------

//test FindLessonsWithFullRoom--------------------------------------------------------------------------------------------------
    public function testFindLessonsWithFullRoomFound()
    {
        $this->db = new SQLite3("./tests/dbForTesting2.db");
        $this->updateDates();
        $sqlfake = "select idLesson from lessons where idLesson=7";
        $fakesqlite3Result = $this->db->query($sqlfake);
        $db = $this->getMockBuilder(\SQLite3::class)->disableOriginalConstructor()->setMethods(array('query'))->getMock();
        $db->expects($this->any())->method('query')->will($this->returnValue($fakesqlite3Result));

        $this->gatewayStudentBooking = new Server\api\GatewaysStudentBooking($db);
        $dataExcepted = array();

        $result = $this->gatewayStudentBooking->findLessonsWithFullRoom();
        $this->assertIsArray($result);
    }

    public function testFindLessonsWithFullRoomNotFound()
    {
        $this->db = new SQLite3("./tests/dbForTesting.db");
        $this->gatewayStudentBooking = new Server\api\GatewaysStudentBooking($this->db);
        $dataExcepted = array();
        $result = $this->gatewayStudentBooking->findLessonsWithFullRoom();
        $this->assertEquals(0, $result);
    }
//----------------------------------------------------------------------------------------------------------------------------------

//test FindDetailsOfLessons---------------------------------------------------------------------------------------------------------
    public function testfindDetailsOfLessonsFound()
    {
        $this->db = new SQLite3("./tests/dbForTesting2.db");
        $this->updateDates();
        $this->gatewayStudentBooking = new Server\api\GatewaysStudentBooking($this->db);
        $arrayForTest = array(1, 2, 3);
        $result = $this->gatewayStudentBooking->findDetailsOfLessons($arrayForTest);
        $this->assertIsArray($result);
    }

    public function testfindDetailsOfLessonsNotFound()
    {
        $this->db = new SQLite3("./tests/dbForTesting.db");
        $this->gatewayStudentBooking = new Server\api\GatewaysStudentBooking($this->db);
        $arrayForTest = array(1, 2, 3);
        $result = $this->gatewayStudentBooking->findDetailsOfLessons($arrayForTest);
        $this->assertEquals(0, $result);
    }
//---------------------------------------------------------------------------------------------------------------------------------------

//test InsertNewBooking-------------------------------------------------------------------------------------------------------------------
    public function testInsertNewBookingWorked()
    {
        $this->db = new SQLite3("./tests/dbForTesting.db");
        $this->restoreDB();
        $this->gatewayStudentBooking = new Server\api\GatewaysStudentBooking($this->db);
        $bookForTest = new class

        {
            public $idUser = 20;
            public $idLesson = 20;
            public $date = "2021-12-20 11:00:00";
        };
        $result = $this->gatewayStudentBooking->insertBooking($bookForTest);
        $this->assertIsArray($result);
        $this->assertIsInt($result["bookingId"]);
    }
//-----------------------------------------------------------------------------------------------------------------------------------------

//test UpdateBooking-----------------------------------------------------------------------------------------------------------------------
    public function testUpdateBooking()
    {
        $this->db = new SQLite3("./tests/dbWaiting.db");
        $this->updateDates();
        $this->gatewayStudentBooking = new Server\api\GatewaysStudentBooking($this->db);

        $this->restoreValues();

        $bookingWaiting = $this->gatewayStudentBooking->updateBooking(4);
        $this->assertIsInt($bookingWaiting);
        $this->assertEquals(0, $bookingWaiting);

        $bookingWaiting = $this->gatewayStudentBooking->updateBooking(2);
        $this->assertIsInt($bookingWaiting);
        $this->assertEquals(3, $bookingWaiting);

        $this->restoreValues();
    }
//-------------------------------------------------------------------------------------------------------------------------------------------

//test Useful for testing--------------------------------------------------------------------------------------------------------------------
    protected function restoreDB()
    {
        $this->db->exec('DROP TABLE IF EXISTS "lessons";
        CREATE TABLE IF NOT EXISTS "lessons" (
            "idLesson"	INTEGER,
            "idCourse"	INTEGER,
            "idTeacher"	INTEGER,
            "idClassRoom"	INTEGER,
            "date"	TEXT,
            "beginTime"	TEXT,
            "endTime"	TEXT,
            "type"	TEXT,
            "inPresence" INTEGER,
            "active"	INTEGER,
            PRIMARY KEY("idLesson" AUTOINCREMENT)
        );
        DROP TABLE IF EXISTS "ClassRoom";
        CREATE TABLE IF NOT EXISTS "ClassRoom" (
            "idClassRoom"	INTEGER,
            "totalSeats"	INTEGER,
            PRIMARY KEY("idClassRoom" AUTOINCREMENT)
        );
        DROP TABLE IF EXISTS "enrollment";
        CREATE TABLE IF NOT EXISTS "enrollment" (
            "idEnrollment"	INTEGER,
            "idUser"	INTEGER,
            "idCourse"	INTEGER,
            PRIMARY KEY("idEnrollment" AUTOINCREMENT)
        );
        DROP TABLE IF EXISTS "booking";
        CREATE TABLE IF NOT EXISTS "booking" (
            "idBooking"	INTEGER,
            "idUser"	INTEGER,
            "idLesson"	INTEGER,
            "active"	INTEGER,
            "date"	TEXT,
            "isWaiting"	INTEGER,
            PRIMARY KEY("idBooking" AUTOINCREMENT)
        );
        DROP TABLE IF EXISTS "courses";
        CREATE TABLE IF NOT EXISTS "courses" (
            "idCourse"	INTEGER,
            "idTeacher"	INTEGER,
            "name"	TEXT,
            PRIMARY KEY("idCourse" AUTOINCREMENT)
        );
        DROP TABLE IF EXISTS "users";
        CREATE TABLE IF NOT EXISTS "users" (
            "idUser"	INTEGER NOT NULL UNIQUE,
            "userName"	TEXT,
            "password"	TEXT,
            "role"	TEXT,
            "name"	TEXT,
            PRIMARY KEY("idUser" AUTOINCREMENT)
        );');
    }

    protected function restoreValues()
    {
        $this->db->exec('UPDATE booking SET active=1; UPDATE booking SET isWaiting=1 WHERE idBooking>2;');
    }

    protected function updateDates()
    {
        $this->db->exec("UPDATE booking SET date=datetime('now', '3 days')");
        $this->db->exec("UPDATE lessons SET date=date('now', '3 days');");
    }
}
