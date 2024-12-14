import React from "react";

/**
 * Функция для проверки пароля на соответствие требованиям.
 * 
 * Эта функция проверяет, что пароль:
 * - имеет длину не менее заданного минимального значения `l`
 * - содержит хотя бы одну цифру
 * - содержит хотя бы одну заглавную букву
 * - содержит хотя бы одну строчную букву
 * - состоит только из букв и цифр
 * 
 * @param pass Пароль для проверки.
 * @param l Минимальная длина пароля.
 * @returns `true`, если пароль не соответствует требованиям, иначе `false`.
 */
const showMissmatch = (pass:string, l:number) :boolean => { 
  if (pass.length < l) return true;

  const hasNumeric = /[0-9]/.test(pass);
  const hasUpperCase = /[A-Z]/.test(pass);
  const hasLowerCase = /[a-z]/.test(pass);
  const isAlphaNumerical = /[a-zA-Z0-9]+$/.test(pass);

  return !(hasNumeric && hasUpperCase && hasLowerCase && isAlphaNumerical);
}

/**
 * Компонент поля ввода для пароля.
 * 
 * Этот компонент отображает поле для ввода пароля с валидацией на минимальную длину
 * и соответствие требованиям (наличие цифр, заглавных и строчных букв). Если введённый пароль
 * не соответствует требованиям, отображается сообщение об ошибке.
 * 
 * @param minLen Минимальная длина пароля.
 * @param passSetter Функция для обновления состояния пароля в родительском компоненте.
 * @returns JSX элемент с полем для ввода пароля и сообщением об ошибке (если необходимо).
 */
const PassField_1: React.FC<{
  minLen: number,
  passSetter: (v: string) => void,
}> = ({minLen, passSetter}) => {
  const errMsg = "Поле содержит некоректные данные и не может быть короче 8 символов. Используйте символы a-z, A-Z, 0-9.";

  const [isErr, setIsErr] = React.useState(false);

  /**
   * Обработчик изменения значения в поле пароля.
   * 
   * При изменении значения в поле проверяется, соответствует ли пароль заданным требованиям.
   * Если пароль некорректный, отображается сообщение об ошибке.
   * 
   * @param e Событие изменения значения в поле ввода пароля.
   */
  const handleChange = (e: React.ChangeEvent<HTMLInputElement>): void => {
    const password = e.target.value;
    setIsErr(showMissmatch(e.target.value, minLen));
    passSetter(password);
  }

  return (
    <>
      <div>
        <label htmlFor="pass_1">Пароль</label>
        <input 
          id="pass_1" 
          type="password" 
          name="pass" 
          onChange={handleChange}
          minLength={minLen}/>
      </div>
      {isErr && <div>{errMsg}</div>}
    </>
  );
}

export default PassField_1;