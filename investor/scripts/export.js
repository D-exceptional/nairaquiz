// Display Success Message
export function displaySuccess(msg) {
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-right',
    icon: 'success', // Change from 'type' to 'icon'
    title: msg, // Use 'title' for the message
    timer: 3000,
    showCancelButton: false,
    showConfirmButton: false
  });
  Toast.fire();
}

// Display Error Message
export function displayInfo(info) {
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-right',
    icon: 'info', // Change from 'type' to 'icon'
    title: info, // Use 'title' for the message
    timer: 3000,
    showCancelButton: false,
    showConfirmButton: false
  });
  Toast.fire();
}
