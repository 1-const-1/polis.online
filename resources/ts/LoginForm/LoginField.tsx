import React from "react"

const LoginField: React.FC<{}> = () => {
  const errMsg = "Login is empty";

  const [isErr, setIsErr] = React.useState(false);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>): void => {
    const v = e.target.value;
    !v ? setIsErr(true) : setIsErr(false);
  }

  return (
    <>
      <div>
        <label htmlFor="login">Login</label>
        <input 
          type="text" 
          id="login" 
          name="login" 
          onChange={handleChange}/>
      </div>
      {isErr && <div>{errMsg}</div>}
    </>
  );
}

export default LoginField;