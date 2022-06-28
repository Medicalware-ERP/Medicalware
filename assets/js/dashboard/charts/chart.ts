import {$} from "../../utils"
import Chart, {ChartConfiguration} from 'chart.js/auto';
import Routing from "../../Routing";
import axios from "axios";

const usersChartCanvas = $("#users");
const url = Routing.generate('stats');

const initChartDoughnut = async () => {

    const labels : any = [];

    let results = await axios.request(
        {
            method: 'GET',
            url,
        }
    )
    labels.push(results.data);
    const datasKeys = Object.keys(labels[0]);
    const dataValues = Object.values(labels[0]);
    const data = {
        labels: datasKeys,
        datasets: [{
            label: 'Nombre d\'utilisateurs',
            backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(54, 162, 235)',
                'rgb(255, 205, 86)'
            ],
            hoverOffset: 4,
            data: dataValues,
        }]
    };
    const plugin = {
        id: 'custom_canvas_background_color',
        beforeDraw: (chart : any) => {
            const ctx = chart.canvas.getContext('2d');
            ctx.save();
            ctx.globalCompositeOperation = 'destination-over';
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, chart.width, chart.height);
            ctx.restore();
        }
    };

    const config = {
        type: 'doughnut',
        data: data,
        plugins: [plugin],
        options: {}
    };

    new Chart(usersChartCanvas as HTMLCanvasElement, config as ChartConfiguration);
}

initChartDoughnut();