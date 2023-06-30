import moment from "moment/moment";

export function FormatDate(value) {
    if (value) {
        return moment(value).format('DD/MM/YYYY');
    }
}