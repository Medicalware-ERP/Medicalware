import {$, colors, getCssVariableValue} from '../../utils'

const progressiveBarAnimation = (dataAttributeElement : string, nameDataSet : string, color: string) => {
    const number: HTMLSpanElement = $(dataAttributeElement) as HTMLSpanElement;

    const circle: SVGCircleElement = number.parentElement?.parentElement?.parentElement?.querySelector("circle") as SVGCircleElement;
    circle.style.stroke = color;

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



progressiveBarAnimation("[data-count-users]","countUsers", colors.success);
progressiveBarAnimation("[data-count-doctors]","countDoctors", colors.warning);
progressiveBarAnimation("[data-count-patients]","countPatients", colors.danger);