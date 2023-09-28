import toastr from "toastr";
import "toastr/build/toastr.min.css";
export function useToastr() {
    toastr.options.positionClass = "toast-top-center";
    toastr.options.progressBar = true;
    toastr.options.closeDuration = 2;
    return toastr;
}
