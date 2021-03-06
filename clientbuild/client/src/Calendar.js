import React from 'react';
import FullCalendar from '@fullcalendar/react'
import timeGridPlugin from '@fullcalendar/timegrid';
import ModalStudent from './ModalStudent';
import ModalTeacher from './ModalTeacher';
import API from './API';

class Calendar extends React.Component{
    constructor(props) {
        super(props);
        
        this.state = {
          showModal: false,
          lectureTitle: undefined,
          lectureBeginTime: "",
          lectureEndTime: "",
          lectureDate: undefined,
          lectureClassroom: undefined,
          elementId: undefined,
          inPresence: undefined,
          studentList: [],
        };
      }

    closeModal = () => {
        this.setState({showModal: false});
    }

    render(){
        return <>
            <FullCalendar
                plugins={[ timeGridPlugin ]}
                initialView="timeGridWeek"
                events={this.props.events}
                eventClick={this.handleEventClick}
                contentHeight="auto"
                slotMinTime="08:00:00"
                slotMaxTime="20:00:00"
                allDaySlot={false}
                slotEventOverlap={false}
                eventContent= { function(info) {
                  return <><p>{info.event.title}</p>
                   {info.event.extendedProps.inPresence === 0 ?
                    null
                    :
                    <p className={"classroom"}>{"Classroom: " + info.event.extendedProps.classroom}</p>
                   }
                    </>;
                }}
            />
            {this.props.view === "student" ?
              <ModalStudent 
              show={this.state.showModal} 
              closeModal = {this.closeModal} 
              lectureTitle = {this.state.lectureTitle}
              lectureDate = {this.state.lectureDate}
              lectureBeginTime = {this.state.lectureBeginTime}
              lectureEndTime = {this.state.lectureEndTime}
              lectureClassroom = {this.state.lectureClassroom}
              elementId = {this.state.elementId}
              lectureColor = {this.state.lectureColor}
              bookASeat = {this.props.bookASeat}
              deleteBooking = {this.props.deleteBooking}
              lectureType = {this.state.lectureType}
              peopleWaiting = {this.state.peopleWaiting}
              ></ModalStudent>
            :
              <ModalTeacher
                show={this.state.showModal} 
                closeModal = {this.closeModal} 
                lectureTitle = {this.state.lectureTitle}
                lectureDate = {this.state.lectureDate}
                lectureBeginTime = {this.state.lectureBeginTime}
                lectureEndTime = {this.state.lectureEndTime}
                lectureClassroom = {this.state.lectureClassroom}
                elementId = {this.state.elementId}
                studentList = {this.state.studentList}
                deleteLecture = {this.props.deleteLecture}
                changeToOnline = {this.props.changeToOnline}
                lectureColor = {this.state.lectureColor}
                dateStart = {new Date(this.state.lectureDate + "T" + this.state.lectureBeginTime)}
                />
            }
            
        </>
    	}
    	
    	handleEventClick = (info) => {
        //not useful right now
        //here info.event is the event object (with id, title, ...)
        //console.log(info.event.id);
        
        if(this.props.view === "teacher") {
          API.getBooking(info.event.id).then((students) => {
              this.setState({studentList: students});
          }
          );
        }
        //if(info.event.extendedProps.type !== "fullLecture") {
          this.setState({showModal: true});
          this.setState({lectureTitle: info.event.title});
          let beginTime = info.event.start.toLocaleTimeString();
          let endTime = info.event.end.toLocaleTimeString();
          let date = info.event.start.toISOString().slice(0,10);
          this.setState({lectureBeginTime: beginTime});
          this.setState({lectureEndTime: endTime});
          this.setState({lectureDate: date});
          this.setState({lectureClassroom: info.event.extendedProps.classroom});
          this.setState({elementId: info.event.id});
          this.setState({lectureColor: info.event.backgroundColor});
          this.setState({lectureType: info.event.extendedProps.type});
          this.setState({peopleWaiting: info.event.extendedProps.peopleWaiting});
        //}
    }
}

export default Calendar;
