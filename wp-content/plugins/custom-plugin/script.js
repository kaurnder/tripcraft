
document.addEventListener('DOMContentLoaded', function () {

    // edit user role modal
    // const editButtonsrole = document.querySelectorAll('.role-btn');
    // editButtonsrole.forEach(button => {
    //     button.addEventListener('click', function () {
    //         const roleName = this.getAttribute('data-role-name');
    //         const roleSlug = this.getAttribute('data-role-slug');
    //         document.getElementById('roles_name').value = roleName;
    //         document.getElementById('custom_role_slug').value = roleSlug;
    //     });
    // });

    //update user data fetch user data in edit modal
    const editButtons = document.querySelectorAll('.edit-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', function () {
        document.querySelector('input[name="f_name"]').value = this.dataset.firstName;
        document.querySelector('input[name="l_name"]').value = this.dataset.lastName;
        document.querySelector('input[name="user_email"]').value = this.dataset.email;
        document.querySelector('input[name="edit_pass"]').value = this.dataset.pass;
        document.querySelector('select[name="roles"]').value = this.dataset.role.trim();
        document.getElementById('edit_user_id').value = this.dataset.userId;
        });
    });

    // add user validation modal show
    var modalElement = document.getElementById('exampleModal');
    if (modalElement.classList.contains('modal-error')){
        var myModal = new bootstrap.Modal(modalElement);
        myModal.show();
    }

    //edit user valiation moal show
    const modal = document.getElementById('staticBackdrop');
    if (modal.classList.contains('modal-error')) {
        const myModal = new bootstrap.Modal(modal);
        myModal.show();
    }

    document.getElementById('user-search').value = '';

});


// Example JavaScript to populate the modal form
// document.querySelectorAll('.edit-role-button').forEach(button => {
//     button.addEventListener('click', function() {
//         const roleSlug = this.dataset.slug;
//         const roleName = this.dataset.name;
//         document.getElementById('custom_role_slug').value = roleSlug;
//         document.getElementById('roles_name').value = roleName;

//     });
// });


 document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('select-all-users');
        const checkboxes = document.querySelectorAll('.user-checkbox');

        selectAll.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        });
    });
  
   

 














