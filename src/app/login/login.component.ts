import { Component, OnInit } from '@angular/core';
import { UsersService } from "../users/users.service";
import { Router } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit {

  email!: string;
  password!: string;

  constructor(public userService: UsersService, public router: Router) { }

  ngOnInit(): void {
  }

  login() {
    const user = {
      email_emp: "eflores@cinco.net",
      contrasena: "123"
      // email: this.email,
      // password: this.password
    };

    this.userService.login(user).subscribe(data => {
      this.userService.setToken(data.token);
      this.router.navigateByUrl('/home');
    },
      error => {
        console.log(error);
      });

  }
}
