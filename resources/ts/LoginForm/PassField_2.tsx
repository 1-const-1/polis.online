import React from "react"

/**
 * Компонент поля для повторного ввода пароля.
 * 
 * Этот компонент отображает поле для повторного ввода пароля и проверяет, совпадает ли введённое значение с
 * ранее введённым паролем. Если пароли не совпадают, отображается сообщение об ошибке.
 * 
 * @param minLen Минимальная длина пароля.
 * @param pass Пароль, введённый в первом поле.
 * @returns JSX элемент с полем для повторного ввода пароля и сообщением об ошибке (если пароли не совпадают).
 */
const PassField_2: React.FC<{
  minLen: number,
  pass: string,
}> = ({minLen, pass}) => {
  const errMsg = "Пароли не совподают";

  const [isErr, setIsErr] = React.useState(false);
  
  /**
   * Обработчик изменения значения в поле для повторного пароля.
   * 
   * При изменении значения в поле проверяется, совпадает ли повторный пароль с введённым ранее.
   * Если пароли не совпадают, отображается сообщение об ошибке.
   * 
   * @param e Событие изменения значения в поле повторного пароля.
   */
  const handleChange = (e: React.ChangeEvent<HTMLInputElement>): void => {
    const rpass = e.target.value;
    pass !== rpass ? setIsErr(true) : setIsErr(false);
  }

  return (
    <>
      <div>
        <label htmlFor="pass_2">Повторите пароль</label>
        <input 
          id="pass_2" 
          type="password" 
          name="rpass"
          onChange={handleChange}
          minLength={minLen} />
      </div>
      {isErr && <div>{errMsg}</div>}
    </>
  );
}

export default PassField_2;