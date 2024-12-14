import React from "react"

/**
 * Компонент поля для ввода логина с валидацией.
 * 
 * Этот компонент отображает поле ввода для логина. Если поле не заполнено, 
 * отображается сообщение об ошибке.
 *
 * @component
 * @example
 * return <LoginField />
 */
const LoginField: React.FC<{}> = () => {
  const errMsg = "Поле не заполнено";

  const [isErr, setIsErr] = React.useState(false);

  /**
   * Обработчик изменения значения в поле ввода.
   * 
   * При каждом изменении значения проверяется, заполнено ли поле. Если оно пустое, 
   * то устанавливается состояние ошибки.
   * 
   * @param e Событие изменения значения поля ввода
   */
  const handleChange = (e: React.ChangeEvent<HTMLInputElement>): void => {
    const v = e.target.value;
    !v ? setIsErr(true) : setIsErr(false);
  }

  return (
    <>
      <div>
        <label htmlFor="login">Логин</label>
        <input 
          type="text" 
          id="login" 
          name="login" 
          onChange={handleChange}/>
      </div>
      {isErr && <div className="err-msg">{errMsg}</div>}
    </>
  );
}

export default LoginField;