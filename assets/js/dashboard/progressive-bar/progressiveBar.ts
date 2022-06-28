import {$} from '../../utils'

const progressiveBarAnimation = (dataAttributeElement : string, nameDataSet : string) => {
    const number: HTMLSpanElement = $(dataAttributeElement) as HTMLSpanElement;

    let counter = 0;
    setInterval(() => {
        if(counter == Number(number.dataset[nameDataSet])){
          clearInterval();
      } else {
          counter += 1;
          number.innerHTML = counter.toString();
      }
    }, 15)
}



progressiveBarAnimation("[data-count-users]","countUsers");
progressiveBarAnimation("[data-count-doctors]","countDoctors");
progressiveBarAnimation("[data-count-patients]","countPatients");