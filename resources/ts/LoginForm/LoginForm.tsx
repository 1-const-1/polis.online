import React from "react";
import LoginField from "./LoginField";
import PassField_1 from "./PassField_1";
import PassField_2 from "./PassField_2";

import "../../sass/LoginForm/LoginForm.sass";

/**
 * Функция для отправки данных формы по указанному URI.
 * 
 * Эта функция проверяет данные формы, валидирует их с помощью предоставленного валидатора,
 * и отправляет данные на сервер.
 * 
 * @param form HTML форма, данные которой будут отправлены.
 * @param url URI адрес, по которому будет отправлен запрос.
 * @param validator Функция, которая проверяет, соответствуют ли данные определённым критериям.
 * @returns Промис, который разрешается после отправки данных на сервер.
 */
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

/**
 * Функция для обработки отправки формы в режиме входа.
 * 
 * Эта функция передаёт данные формы на сервер по URI "/login".
 * Проверяется, что поле "login" не пустое перед отправкой.
 * 
 * @param form HTML форма, данные которой будут отправлены.
 * @returns Промис, который разрешается после отправки данных на сервер.
 */
const onLogin = (form: HTMLFormElement) => 
  handleFormSubmission(form, "/login", (data) => !!data["login"]);

/**
 * Функция для обработки отправки формы в режиме регистрации.
 * 
 * Эта функция передаёт данные формы на сервер по URI "/signup".
 * Проверяется, что поля "login" и "pass" заполнены, и что пароль и повторный пароль совпадают.
 * 
 * @param form HTML форма, данные которой будут отправлены.
 * @returns Промис, который разрешается после отправки данных на сервер.
 */
const onSignup = (form: HTMLFormElement) => 
  handleFormSubmission(form, "/signup", (data) => !!data["login"] && data["pass"] === data["rpass"]);

const LoginForm: React.FC<{}> = () => {

const loginMsg = "При наличии аккаунта";
const signupMsg = "Если впервые, то";

const ctxLogin = "Войти";
const ctxSignup = "Создать";

const [pass, setPass] = React.useState("");
const [loginMode, setLoginMode] = React.useState(false);

/**
 * Обработчик отправки формы. В зависимости от режима, отправляет данные либо на вход, либо на регистрацию.
 * 
 * @param e Событие отправки формы.
 */
const handleFormSubmit = (e: React.FormEvent<HTMLFormElement>): void => {
  e.preventDefault();
  const form = e.target as HTMLFormElement;
  !loginMode ? onSignup(form) : onLogin(form);
}

/**
 * Переключает между режимами входа и регистрации.
 */
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