import React from "react";

const showMissmatch = (pass:string, l:number) :boolean => { 
  if (pass.length < l) return true;

  const hasNumeric = /[0-9]/.test(pass);
  const hasUpperCase = /[A-Z]/.test(pass);
  const hasLowerCase = /[a-z]/.test(pass);
  const isAlphaNumerical = /[a-zA-Z0-9]+$/.test(pass);

  return !(hasNumeric && hasUpperCase && hasLowerCase && isAlphaNumerical);
}

const PassField_1: React.FC<{
  minLen: number,
  passSetter: (v: string) => void,
}> = ({minLen, passSetter}) => {
  const errMsg = "Password invalid. Use only a-z A-Z 0-9 letters";

  const [isErr, setIsErr] = React.useState(false);

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>): void => {
    const password = e.target.value;
    setIsErr(showMissmatch(e.target.value, minLen));
    passSetter(password);
  }

  return (
    <>
      <div>
        <label htmlFor="pass_1">Password</label>
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