import React from "react";
import LoginField from "./LoginField";
import PassField_1 from "./PassField_1";
import PassField_2 from "./PassField_2";

import "../../sass/LoginForm/LoginForm.sass";

const handleFormSubmission = async (
  form: HTMLFormElement, 
  url: string,
  validator: (data: {[key:string]: FormDataEntryValue}) => boolean
): Promise<void> => {
  if (!form) {
    console.error("The element is not the form");
    return;
  }

  const data = Object.fromEntries((new FormData(form)).entries());
  if (!validator(data)) {
    console.error("The enetered credentials are not valid");
    return;
  }

  const CSRF_TOKEN = document.querySelector<HTMLInputElement>("[name='_token']");
  if (!CSRF_TOKEN) {
    console.error("CSRF token not found");
    return;
  }

  try {
    const res: Response = await fetch (url, {

      method: "post",
      headers:{
        "content-type" :  "application/json",
        "x-csrf-token" : CSRF_TOKEN.value,
      },
      body: JSON.stringify(data),

    });

    if (!res.ok) {
      throw new Error(`Unsuccessful response. Status: ${res.status}`);
    }

  } catch (err) {
    console.error(`Error during form submission: ${err}`);
  }
}

const onLogin = (form: HTMLFormElement) => 
  handleFormSubmission(form, "/login", (data) => !!data["login"]);

const onSignup = (form: HTMLFormElement) => 
  handleFormSubmission(form, "/signup", (data) => !!data["login"] && data["pass"] === data["rpass"]);

const LoginForm: React.FC<{}> = () => {

const loginMsg = "If you have an account";
const signupMsg = "If you don't have one";

const ctxLogin = "Log in";
const ctxSignup = "Sign up";

const [pass, setPass] = React.useState("");
const [loginMode, setLoginMode] = React.useState(false);

const handleFormSubmit = (e: React.FormEvent<HTMLFormElement>): void => {
  e.preventDefault();
  const form = e.target as HTMLFormElement;
  !loginMode ? onSignup(form) : onLogin(form);
}

const handleSpanClick = (): void => {
  setLoginMode(!loginMode);
}

return (
<div className="form-cnt">
  <form 
    id="l-form"
    className="login-form"
    onSubmit={handleFormSubmit}>

    <LoginField />

    <PassField_1 
      minLen={8}
      passSetter={setPass}/>

    {!loginMode && <PassField_2 minLen={8} pass={pass}/>}

    <div className="login-mode">
      <div>{!loginMode ? signupMsg : loginMsg}</div>
      <span onClick={handleSpanClick}>{!loginMode ? ctxLogin : ctxSignup}</span>
    </div>

    <div className="login-submit">
      <input 
        type="submit" 
        value={!loginMode ? ctxSignup : ctxLogin}/>
    </div>

  </form>
</div>
);
}

export default LoginForm;