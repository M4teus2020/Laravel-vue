import toastr from 'toastr';
import 'toastr/build/toastr.css';

export function UseToastr() {
    toastr.options = {
            "positionClass": "toast-bottom-right",
            'closeButton': true,
            'progressBar': true
        };
    return toastr;
}